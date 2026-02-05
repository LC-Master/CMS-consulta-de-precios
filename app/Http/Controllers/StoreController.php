<?php

namespace App\Http\Controllers;

use ParagonIE\Paseto\Keys\AsymmetricSecretKey;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Purpose;
use ParagonIE\Paseto\Protocol\Version4;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use App\Models\Store;
use Illuminate\Support\Facades\Http;
use App\Enums\SyncStatusEnum;
use Illuminate\Routing\Controllers\Middleware;
use App\Actions\Media\StorePlaceholderAction;

class StoreController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:store.list', only: ['index']),
        ];
    }

    public function index()
    {
        try {
            $query = Store::with('syncState.placeholder', 'centerMediaErrors');

            if ($search = request()->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('Name', 'like', "%{$search}%")
                        ->orWhere('StoreCode', 'like', "%{$search}%")
                        ->orWhere('Region', 'like', "%{$search}%");
                });
            }

            if ($status = request()->input('status')) {
                $query->whereHas('syncState', function ($q) use ($status) {
                    $q->where('sync_status', $status);
                });
            }

            return Inertia::render('Store/Index', [
                'stores' => Inertia::scroll($query->orderBy('ID', 'desc')->paginate(10)->withQueryString()->through(fn($store) => [
                    'id' => $store->id,
                    'name' => $store->Name,
                    'store_code' => $store->StoreCode,
                    'region' => $store->Region,
                    'sync_state' => $store->syncState ? [
                        'id' => $store->syncState->id,
                        'last_synced_at' => $store->syncState->last_synced_at,
                        'sync_status' => $store->syncState->sync_status,
                        'url' => $store->syncState->url,
                        'sync_started_at' => $store->syncState->sync_started_at,
                        'sync_ended_at' => $store->syncState->sync_ended_at,
                        'disk' => $store->syncState->disk,
                        'uptimed_at' => $store->syncState->uptimed_at,
                        'last_reported_at' => now(),
                        'placeholder' => $store->syncState->placeholder ? [
                            'id' => $store->syncState->placeholder->id,
                            'mime_type' => $store->syncState->placeholder->mime_type,
                        ] : null,
                    ] : null,
                    'center_media_errors' => $store->centerMediaErrors,
                ])),
                'filters' => request()->only(['search', 'status']),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Ocurrió un error al cargar las tiendas");
        }
    }
    public function updateSyncUrl(Request $request, Store $store)
    {
        $request->validate([
            'url' => ['required', 'url', 'max:255'],
        ]);

        if ($store->syncState) {
            $store->syncState()->update([
                'url' => $request->input('url'),
            ]);
        } else {
            $store->syncState()->create([
                'url' => $request->input('url'),
                'sync_status' => SyncStatusEnum::PENDING->value,
            ]);
        }

        return redirect()->back()->with('success', "URL de sincronización actualizada para la tienda: {$store->name}");
    }
    public function forceSync(Store $store)
    {
        try {

            $url = $store->syncState->getAttribute('url') ?? null;
            $key = $store->syncState->getAttribute('communication_key') ?? null;

            if (!$url && !$key) {
                return back()->with('error', "La tienda: {$store->getAttribute('Name')} no tiene URL o clave de comunicación configurada");
            }

            $response = $this->sendCommand(
                $key,
                rtrim($url, '/') . '/api/sync/force',
                ['force' => true],
                (string) $store->getKey()
            );

            if ($response->failed()) {
                \Log::error('Error al forzar la sincronización.', ['error' => $response->body()]);

                return back()->with('error', "Error al enviar el comando de sincronización para la tienda: {$store->getAttribute('Name')}");
            }
            if ($response->successful()) {
                return back()->with('success', "Sincronización forzada para la tienda: {$store->getAttribute('Name')}");
            }
        } catch (\Exception $e) {
            \Log::error('Error al forzar la sincronización.', ['error' => $e->getMessage()]);
            return back()->with('error', "Error al forzar la sincronización para la tienda: {$store->getAttribute('Name')}");
        }
    }
    public function forceToken(Store $store)
    {
        try {
            $url = $store->syncState->getAttribute('url') ?? null;
            $key = $store->syncState->getAttribute('communication_key') ?? null;

            if (!$url && !$key) {
                return back()->with('error', "La tienda: {$store->getAttribute('Name')} no tiene URL o clave de comunicación configurada");
            }

            $response = $this->sendCommand(
                $key,
                rtrim($url, '/') . '/api/sync/force/token',
                ['generate' => true],
                (string) $store->getKey()
            );

            if ($response->failed()) {
                \Log::error('Error al forzar la generación del token.', ['error' => $response->body()]);

                return back()->with('error', "Error al enviar el comando de generación del token. para la tienda: {$store->getAttribute('Name')}");
            }
            if ($response->successful()) {
                return back()->with('success', "Token generado para la tienda: {$store->getAttribute('Name')}");
            }
        } catch (\Exception $e) {
            \Log::error('Error al forzar la generación del token.', ['error' => $e->getMessage()]);
            return back()->with('error', "Error al forzar la generación del token para la tienda: {$store->getAttribute('Name')}");
        }
    }
    public function sendCommand(string $key, string $endpoint, array $payload, string $storeId)
    {
        $seed = sodium_hex2bin($key);

        $keypair = sodium_crypto_sign_seed_keypair($seed);

        $secretKey = sodium_crypto_sign_secretkey($keypair);

        $privateKey = new AsymmetricSecretKey($secretKey);

        $token = (new Builder())
            ->setKey($privateKey)
            ->setVersion(new Version4())
            ->setPurpose(Purpose::public())
            ->setIssuedAt()
            ->setExpiration((new \DateTime())->add(new \DateInterval('PT2H')))
            ->setClaims([
                'store_id' => $storeId,
                'iat' => now()->toIso8601String(),
            ]);
        return Http::withToken($token)
            ->post($endpoint, $payload);
    }
    public function updatePlaceholder(Request $request, Store $store, StorePlaceholderAction $action)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:mp4,jpg,jpeg,png,webp', 'max:153600'],
        ]);

        $action->execute(
            $store,
            $request->file('file'),
        );

        return redirect()->back()->with('success', "Placeholder actualizado para la tienda: {$store->name}");
    }
}
