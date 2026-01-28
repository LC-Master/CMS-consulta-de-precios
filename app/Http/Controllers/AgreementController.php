<?php

namespace App\Http\Controllers;

use App\Actions\Agreement\CreateAgreementAction;
use App\Enums\AgreementStatus;
use App\Http\Requests\Agreement\StoreAgreementRequest;
use App\Http\Requests\Agreement\UpdateAgreementRequest;
use App\Models\Agreement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AgreementController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            'index' => ['permission:agreements.list'],
            'create' => ['permission:agreements.create'],
            'store' => ['permission:agreements.create'],
            'show' => ['permission:agreements.view'],
            'edit' => ['permission:agreements.update'],
            'update' => ['permission:agreements.update'],
            'destroy' => ['permission:agreements.delete'],
        ];
    }

    public function index(Request $request)
    {
        $query = Agreement::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        return Inertia::render('Agreements/Index', [
            'agreements' => Inertia::scroll($query->latest()->paginate()),
            'filters' => $request->only(['search', 'status']),
            'statuses' => array_reverse(AgreementStatus::cases(), false),
        ]);
    }

    public function create()
    {
        return Inertia::render('Agreements/Create');
    }

    /**
     * Store usa StoreAgreementRequest
     */
    public function store(StoreAgreementRequest $request, CreateAgreementAction $createAgreementAction): RedirectResponse
    {
        try {

            $createAgreementAction->execute($request->validated());

            return to_route('agreement.index')
                ->with('success', 'Convenio creado correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error creating agreement: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear el convenio. Por favor, intente nuevamente.');
        }
    }

    public function show(Agreement $agreement)
    {
        $agreement = [
            'id' => $agreement->getKey(),
            'name' => $agreement->name,
            'legal_name' => $agreement->legal_name,
            'tax_id' => $agreement->tax_id,
            'contact_person' => $agreement->contact_person,
            'contact_email' => $agreement->contact_email,
            'contact_phone' => $agreement->contact_phone,
            'start_date' => $agreement->start_date,
            'end_date' => $agreement->end_date,
            'is_active' => filter_var($agreement->is_active, FILTER_VALIDATE_BOOLEAN),
            'observations' => $agreement->observations,
        ];

        return Inertia::render('Agreements/Show', [
            'agreement' => $agreement,
        ]);
    }

    public function edit(Agreement $agreement)
    {
        return Inertia::render('Agreements/Edit', [
            'agreement' => $agreement,
        ]);
    }

    /**
     * Update usa UpdateAgreementRequest
     */
    public function update(UpdateAgreementRequest $request, Agreement $agreement)
    {
        try {
            $request->validated();

            $agreement->update($request->all());

            return to_route('agreement.index')
                ->with('success', 'Acuerdo actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error updating agreement: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al actualizar el convenio. Por favor, intente nuevamente.');
        }
    }

    public function destroy(Agreement $agreement)
    {
        try {
            $agreement->delete();

            return to_route('agreement.index')
                ->with('success', 'Convenio eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error deleting agreement: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->with('error', 'Ocurrió un error inesperado al eliminar el convenio. Por favor, intente nuevamente.');
        }
    }
}
