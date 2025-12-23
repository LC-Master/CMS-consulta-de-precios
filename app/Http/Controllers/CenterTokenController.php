<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Inertia\Inertia;
use App\Http\Requests\CenterToken\StoreCenterTokenRequest;
use App\Http\Requests\CenterToken\UpdateCenterTokenRequest;

class CenterTokenController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('CenterTokens/Index', [
            'centerTokens' => Inertia::scroll(fn() => PersonalAccessToken::with('tokenable')->latest()->paginate()->through(fn($token) => [
                'id' => $token->id,
                'name' => $token->name ?? null,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'center' => $token->tokenable_type === Center::class && $token->tokenable
                    ? [
                        'id' => $token->tokenable->id,
                        'name' => $token->tokenable->name,
                        'code' => $token->tokenable->code,
                    ]
                    : null,
            ])),
            'filters' => $request->only(['search']),
            'centers' => Center::select('id', 'name', 'code')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('CenterTokens/Create');
    }

    /**
     * Store usa StoreCenterTokenRequest
     */
    public function store(StoreCenterTokenRequest $request)
    {
        //
    }

    public function show(PersonalAccessToken $centerToken)
    {
        return Inertia::render('CenterTokens/Show', [
            'centerToken' => $centerToken,
        ]);
    }

    public function edit(PersonalAccessToken $centerToken)
    {
        return Inertia::render('CenterTokens/Edit', [
            'centerToken' => $centerToken,
        ]);
    }

    /**
     * Update usa UpdateAgreementRequest
     */
    public function update(UpdateCenterTokenRequest $request, PersonalAccessToken $centerToken)
    {
        $centerToken->update($request->validated());

        return to_route('centerTokens.index')
            ->with('success', 'Token actualizado correctamente.');
    }

    public function destroy(PersonalAccessToken $centertoken)
    {
        if (!$centertoken->exists) {
            abort(404, 'Token no encontrado.');
        }

        try {
            $centertoken->delete();

            return back()
                ->with('success', 'Token eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error deleting center token: ' . $e->getMessage(), ['admin_id' => auth()->id()]);
            return back()
                ->with('error', 'Ocurrió un error inesperado al eliminar el token.');
        }
    }
}
