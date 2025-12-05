<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Http\Requests\Agreement\StoreAgreementRequest; 
use App\Http\Requests\Agreement\UpdateAgreementRequest; 
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class AgreementController extends Controller
{
    public function index()
    {
        return Inertia::render('Agreements/Index', [
            'agreements' => Inertia::scroll(fn () => Agreement::paginate()),
        ]);
    }

    public function create()
    {
        return Inertia::render('Agreements/Create');
    }

    /**
     * Store usa StoreAgreementRequest
     */
    public function store(StoreAgreementRequest $request)
    {
        Agreement::create($request->validated());

        return Redirect::route('agreements.index')
            ->with('success', 'Acuerdo creado correctamente.');
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