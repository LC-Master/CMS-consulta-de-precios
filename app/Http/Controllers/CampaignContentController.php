<?php

namespace App\Http\Controllers;

use App\Models\CampaignContent;
use App\Models\Campaign;
use App\Http\Requests\CampaignContent\StoreCampaignContentRequest;
use App\Http\Requests\CampaignContent\UpdateCampaignContentRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class CampaignContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('CampaignContents/Index', [
            'campaigncontent' => Inertia::scroll(CampaignContent::with(['campaign'])->paginate()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CampaignContents/Create', [
            // Enviamos la lista de campañas para el select
            'campaigns' => Campaign::orderBy('name')->select('id', 'name')->get(),
            // Opcional: Si tienes una lista fija de tipos, puedes enviarla también
            'types' => ['video', 'image', 'article', 'landing_page'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignContentRequest $request)
    {
        CampaignContent::create($request->validated());

        return Redirect::route('campaign-contents.index')
            ->with('success', 'Contenido de campaña creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignContent $campaignContent)
    {
        $campaignContent->load('campaign');

        return Inertia::render('CampaignContents/Show', [
            'campaignContent' => $campaignContent
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampaignContent $campaignContent)
    {
        return Inertia::render('CampaignContents/Edit', [
            'campaignContent' => $campaignContent,
            'campaigns' => Campaign::orderBy('name')->select('id', 'name')->get(),
            'types' => ['video', 'image', 'article', 'landing_page'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignContentRequest $request, CampaignContent $campaignContent)
    {
        $campaignContent->update($request->validated());

        return Redirect::route('campaign-contents.index')
            ->with('success', 'Contenido de campaña actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignContent $campaignContent)
    {
        $campaignContent->delete();

        return Redirect::route('campaign-contents.index')
            ->with('success', 'Contenido eliminado correctamente.');
    }
}