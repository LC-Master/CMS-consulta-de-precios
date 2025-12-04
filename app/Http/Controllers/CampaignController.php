<?php

namespace App\Http\Controllers;

use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Campaigns\UpdateCampaignRequest;
use App\Models\Agreement;
use App\Models\Campaign;
use App\Models\Department;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index()
    {
        return Inertia::render('campaign/Index', [
            'campaigns' => Inertia::scroll(fn () => Campaign::with(['status', 'department', 'agreement'])->paginate()),
        ]);
    }

    public function create()
    {
        return Inertia::render('campaign/Create', [
            'statuses' => Status::all(),
            'departments' => Department::all(),
            'agreements' => Agreement::all(),
        ]);
    }

    public function store(StoreCampaignRequest $request)
    {
        $data = $request->validated();

        Campaign::create(array_merge($data, [
            'created_by' => Auth::id(),
        ]));

        return redirect()->route('campaign.index')->with('success', 'Campaña creada correctamente.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->with(['status', 'department', 'agreement']);

        return Inertia::render('campaign/Show', ['campaign' => $campaign]);
    }

    public function edit(Campaign $campaign)
    {
        return Inertia::render('campaign/Edit', [
            'campaign' => $campaign,
            'statuses' => Status::all(),
            'departments' => Department::all(),
            'agreements' => Agreement::all(),
        ]);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $campaign->update($data);

        return redirect()->route('campaign.index')
            ->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

            return redirect()->route('campaign.index')
            ->with('success', 'Campaña eliminada.');
    }
}
