<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Http\Requests\Center\StoreCenterRequest;
use App\Http\Requests\Center\UpdateCenterRequest;
use Inertia\Inertia;

class CenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Center::query();
        $query->where('name','!=','Todo');

        return Inertia::render('Centers/Index', [
            'centers' => Inertia::scroll(fn () => $query->paginate()->withQueryString()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return Inertia::render('Centers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCenterRequest $request)
    {
        Center::create($request->validated());

        return to_route('centers.index')
            ->with('success', 'Centro creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Center $center)
    {
        return Inertia::render('Centers/Show', [
            'center' => $center
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Center $center)
    {
        return Inertia::render('Centers/Edit', [
            'center' => $center
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCenterRequest $request, Center $center)
    {
        $center->update($request->validated());

        return to_route('centers.index')
            ->with('success', 'Centro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Center $center)
    {
        $center->delete();

        return to_route('centers.index')
            ->with('success', 'Centro eliminado correctamente.');
    }
}
