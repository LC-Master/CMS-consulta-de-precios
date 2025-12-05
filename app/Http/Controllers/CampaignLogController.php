<?php

namespace App\Http\Controllers;

use App\Models\CampaignLog;
use App\Models\Campaign;
use App\Http\Requests\CampaignLog\StoreCampaignLogRequest;
use App\Http\Requests\CampaignLog\UpdateCampaignLogRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class CampaignLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return Inertia::render('CampaignLogs/Index', [
            'campaignlog' => Inertia::scroll(fn () => CampaignLog::with(['campaign'])->paginate()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CampaignLogs/Create', [
            'campaigns' => Campaign::orderBy('name')->select('id', 'name')->get(),
            // Opcional: Lista de niveles sugeridos para el select
            'levels' => ['info', 'warning', 'error', 'debug', 'critical'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignLogRequest $request)
    {
        CampaignLog::create($request->validated());

        return Redirect::route('campaign-logs.index')
            ->with('success', 'Log registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignLog $campaignLog)
    {
        $campaignLog->load('campaign');

        return Inertia::render('CampaignLogs/Show', [
            'campaignLog' => $campaignLog
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampaignLog $campaignLog)
    {
        return Inertia::render('CampaignLogs/Edit', [
            'campaignLog' => $campaignLog,
            'campaigns' => Campaign::orderBy('name')->select('id', 'name')->get(),
            'levels' => ['info', 'warning', 'error', 'debug', 'critical'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignLogRequest $request, CampaignLog $campaignLog)
    {
        $campaignLog->update($request->validated());

        return Redirect::route('campaign-logs.index')
            ->with('success', 'Log actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignLog $campaignLog)
    {
        $campaignLog->delete();

        return Redirect::route('campaign-logs.index')
            ->with('success', 'Log eliminado correctamente.');
    }
}