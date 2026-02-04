<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Inertia\Inertia;
use App\Http\Requests\CenterToken\StoreCenterTokenRequest;


class CenterTokenController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:token.list', only: ['index']),
            new \Illuminate\Routing\Controllers\Middleware('permission:token.create', only: ['store']),
            new \Illuminate\Routing\Controllers\Middleware('permission:token.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = PersonalAccessToken::with('tokenable')
            ->where('tokenable_type', Store::class);

        if ($request->filled('store')) {
            $query->where('tokenable_id', $request->input('store'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('tokenable', function ($q2) use ($search): void {
                        $q2->where('Name', 'like', "%{$search}%")
                            ->orWhere('StoreCode', 'like', "%{$search}%");
                    });
            });
        }

        return Inertia::render('CenterTokens/Index', [
            'centerTokens' => Inertia::scroll(fn() => $query->latest()->paginate()->through(fn($token) => [
                'id' => $token->id,
                'name' => $token->name ?? null,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'store' => $token->tokenable_type === Store::class && $token->tokenable
                    ? [
                        'id' => $token->tokenable->id,
                        'name' => $token->tokenable->name,
                        'store_code' => $token->tokenable->store_code,
                    ]
                    : null,
            ])),
            'filters' => $request->only(['search', 'store']),
            'stores' => Store::orderBy('Name')->get(['ID', 'Name', 'StoreCode']),
        ]);
    }

    /**
     * Store usa StoreCenterTokenRequest
     */

    public function store(StoreCenterTokenRequest $request)
    {
        try {

            $request->validated();

            $exists = PersonalAccessToken::where('tokenable_id', $request->input('store_id'))
                ->where('tokenable_type', Store::class)
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->with('error', 'Ya existe un token asociado con este centro. Debe revocarlo antes de crear uno nuevo.');
            }

            $store = Store::findOrFail($request->input('store_id'));

            $token = $store->createToken($request->input('name'))->plainTextToken;

            event(new \App\Events\CenterToken\CenterTokenEvent(
                store: $store,
                type: 'create',
                tokenName: $request->input('name')
            ));

            return back()->with([
                'success' => ['success' => 'Token creado correctamente', 'token' => $token],
            ]);

        } catch (\Throwable $e) {
            Log::error('Error creating center token: ' . $e->getMessage(), ['admin_id' => auth()->id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear el token.');
        }
    }


    public function destroy(PersonalAccessToken $centertoken)
    {
        try {
            $store = $centertoken->getAttribute('tokenable');
            $centertoken->delete();
            event(new \App\Events\CenterToken\CenterTokenEvent(
                store: $store,
                type: 'delete',
                tokenName: $centertoken->getAttribute('name')
            ));
            return back()
                ->with('success', 'Token eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error deleting center token: ' . $e->getMessage(), ['admin_id' => auth()->id()]);
            return back()
                ->with('error', 'Ocurrió un error inesperado al eliminar el token.');
        }
    }
}
