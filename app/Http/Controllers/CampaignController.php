<?php

namespace App\Http\Controllers;

use App\Models\CampaignsModel;
use App\Models\StatusModel;
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\SaveCampaignRequest;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = CampaignsModel::with(['status', 'department', 'agreement'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns
        ]);
    }

    public function create()
    {
        return Inertia::render('Campaigns/Create', [
            'statuses' => StatusModel::all(),
            'departments' => DepartmentsModel::all(),
            'agreements' => AgreementsModel::all(),
        ]);
    }

   public function store(SaveCampaignRequest $request)
{
    $data = $request->validated();
    
    CampaignsModel::create(array_merge($data, [
        'created_by' => Auth::id(),
    ]));

    return redirect()->route('campaigns.index')->with('success', 'Campaña creada correctamente.');
}

    public function show(CampaignsModel $campaign)
    {
        $campaign->with(['status', 'department', 'agreement']);
        return Inertia::render('Campaigns/Show', ['campaign' => $campaign]);
    }

    public function edit(CampaignsModel $campaign)
    {
        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'statuses' => StatusModel::all(),
            'departments' => DepartmentsModel::all(),
            'agreements' => AgreementsModel::all(),
        ]);
    }

    public function update(SaveCampaignRequest $request, CampaignsModel $campaign)
    {
        $data = $request->validated();

        $data['updated_by'] = Auth::id();

        $campaign->update($data);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy(CampaignsModel $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña eliminada.');
    }
}
