<?php

namespace App\Http\Controllers;

use App\Models\CampaignStore;
use App\Models\Campaign;
use App\Models\Center;
use App\Http\Requests\CampaignStore\StoreCampaignStoreRequest;
use App\Http\Requests\CampaignStore\UpdateCampaignStoreRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class CampaignStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return Inertia::render('CampaignStores/Index', [
            'campaignstore' => Inertia::scroll(fn () => CampaignStore::with(['campaign', 'center'])->paginate()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CampaignStores/Create', [
            'campaigns' => Campaign::orderBy('name')->select('id', 'name')->get(),
            'centers' => Center::orderBy('name')->select('id', 'name', 'code')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignStoreRequest $request)
    {
        CampaignStore::create($request->validated());

        return Redirect::route('campaign-stores.index')
            ->with('success', 'Campaña asignada al centro correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignStore $campaignStore)
    {
        $campaignStore->load(['campaign', 'center']);

        return Inertia::render('CampaignStores/Show', [
            'campaignStore' => $campaignStore
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampaignStore $campaignStore)
    {
        return Inertia::render('CampaignStores/Edit', [
            'campaignStore' => $campaignStore,
            'campaigns' => Campaign::orderBy('name')->select('id', 'name')->get(),
            'centers' => Center::orderBy('name')->select('id', 'name', 'code')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignStoreRequest $request, CampaignStore $campaignStore)
    {
        $campaignStore->update($request->validated());

        return Redirect::route('campaign-stores.index')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignStore $campaignStore)
    {
        $campaignStore->delete();

        return Redirect::route('campaign-stores.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }
}