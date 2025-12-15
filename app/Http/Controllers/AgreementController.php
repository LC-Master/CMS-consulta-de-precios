<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Http\Requests\Agreement\StoreAgreementRequest; 
use App\Http\Requests\Agreement\UpdateAgreementRequest; 
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use App\Actions\Agreement\CreateAgreementAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AgreementController extends Controller
{
    public function index(Request $request)
    {
    $query = Agreement::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('tax_id', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('is_active', $request->status);
    }

    return Inertia::render('Agreements/Index', [
        'agreements' => Inertia::scroll(fn () => $query->latest()->paginate()),
        'filters' => $request->only(['search', 'status']),
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
        return Inertia::render('Agreements/Show', [
            'agreement' => $agreement
        ]);
    }

    public function edit(Agreement $agreement)
    {
        return Inertia::render('Agreements/Edit', [
            'agreement' => $agreement
        ]);
    }

    /**
     * Update usa UpdateAgreementRequest
     */
    public function update(UpdateAgreementRequest $request, Agreement $agreement)
    {
        $agreement->update($request->validated());

        return Redirect::route('agreements.index')
            ->with('success', 'Acuerdo actualizado correctamente.');
    }

    public function destroy(Agreement $agreement)
    {
        $agreement->delete();

        return Redirect::route('agreements.index')
            ->with('success', 'Acuerdo eliminado correctamente.');
    }
}