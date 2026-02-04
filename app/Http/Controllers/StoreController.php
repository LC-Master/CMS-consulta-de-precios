<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Store;
use App\Enums\SyncStatusEnum;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
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
        // Lógica para forzar la sincronización del store
        // Por ejemplo, podrías despachar un trabajo o actualizar un campo en la base de datos

        return redirect()->back()->with('success', "Sincronización forzada para la tienda: {$store->name}");
    }
    public function forcePlaylist(Store $store)
    {
        // Lógica para forzar la actualización de la playlist del store
        // Por ejemplo, podrías despachar un trabajo o actualizar un campo en la base de datos

        return redirect()->back()->with('success', "Playlist forzada para la tienda: {$store->name}");
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
