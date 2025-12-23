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
        $query = PersonalAccessToken::with('tokenable')
            ->where('tokenable_type', Center::class);

        if ($request->filled('center')) {
            $query->where('tokenable_id', $request->input('center'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('tokenable', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
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
                'center' => $token->tokenable_type === Center::class && $token->tokenable
                    ? [
                        'id' => $token->tokenable->id,
                        'name' => $token->tokenable->name,
                        'code' => $token->tokenable->code,
                    ]
                    : null,
            ])),
            'filters' => $request->only(['search', 'center']),
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
        try {
            $request->validated();
            $center = Center::findOrFail($request->center_id);

            $token = $center->createToken($request->name)->plainTextToken;

            return back()->with([
                'flash' => ['success' => 'Token creado correctamente', 'token' => $token],
            ]);
        } catch (\Throwable $e) {
            Log::error('Error creating center token: ' . $e->getMessage(), ['admin_id' => auth()->id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear el token.');
        }
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
