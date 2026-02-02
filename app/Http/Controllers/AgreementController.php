<?php

namespace App\Http\Controllers;

use App\Actions\Agreement\CreateAgreementAction;
use App\Actions\Agreement\UpdateAgreementAction; // Asegúrate de importar esto
use App\Enums\AgreementStatus;
use App\Http\Requests\Agreement\StoreAgreementRequest;
use App\Http\Requests\Agreement\UpdateAgreementRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Agreement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Supplier;

class AgreementController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:agreement.list', only: ['index']),
            new Middleware('permission:agreement.show', only: ['show']),
            new Middleware('permission:agreement.create', only: ['create', 'store']),
            new Middleware('permission:agreement.update', only: ['edit', 'update']),
            new Middleware('permission:agreement.delete', only: ['destroy']),
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
        $defaultSuppliers = Supplier::select([
            'id', 
            'SupplierName', 
            'AccountNumber',
            'ContactName', 
            'EmailAddress', 
            'PhoneNumber', 
            'Notes'
        ])
        ->orderBy('SupplierName')
        ->get();

        return Inertia::render('Agreements/Create', [
            'defaultSuppliers' => $defaultSuppliers
        ]);
    }

    public function store(StoreAgreementRequest $request, CreateAgreementAction $createAgreementAction): RedirectResponse
    {
        try {
            $createAgreementAction->execute($request->validated());

            return to_route('agreement.index')
                ->with('success', 'Acuerdo creado correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error creating agreement: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear el acuerdo.');
        }
    }

    public function show(Agreement $agreement)
    {
        $agreementData = [ // Renombrado para evitar conflicto de variables
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
            'agreement' => $agreementData,
        ]);
    }

    public function edit(Agreement $agreement)
    {
        $defaultSuppliers = Supplier::select([
            'id', 'SupplierName', 'AccountNumber', 'ContactName', 'EmailAddress', 'PhoneNumber', 'Notes'
        ])
        ->orderBy('SupplierName')
        ->get();

        $currentSupplier = null;
        if ($agreement->supplier_id) {
            $currentSupplier = Supplier::find($agreement->supplier_id);
            
            // Si el proveedor actual no está en la lista de 50, lo agregamos
            if ($currentSupplier && !$defaultSuppliers->contains('id', $currentSupplier->id)) {
                $defaultSuppliers->push($currentSupplier);
            }
        }

        return Inertia::render('Agreements/Edit', [
            'agreement' => $agreement,
            'defaultSuppliers' => $defaultSuppliers,
        ]);
    }

    public function update(UpdateAgreementRequest $request, Agreement $agreement)
    {
        try {
            $request->validated();
            // Nota: Aquí deberías usar un Action si tienes lógica compleja, 
            // pero el update directo funciona si el fillable está bien.
            $agreement->update($request->all());

            return to_route('agreement.index')
                ->with('success', 'Acuerdo actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error updating agreement: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al actualizar el acuerdo.');
        }
    }

    public function destroy(Agreement $agreement)
    {
        try {
            $agreement->delete();
            return to_route('agreement.index')
                ->with('success', 'Acuerdo eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error deleting agreement: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return back()->with('error', 'Ocurrió un error inesperado al eliminar el acuerdo.');
        }
    }
}