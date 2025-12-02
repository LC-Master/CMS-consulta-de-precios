<?php

namespace App\Http\Controllers;

use App\Models\CampaignsModel;
use App\Models\StatusModel;
use App\Models\DepartmentsModel;
use App\Models\AgreementsModel;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
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

        $data['created_by'] = Auth::id();

        CampaignsModel::create($data);

        return redirect()->route('campaigns.index', ["success" => "Campaña creada correctamente."], 201);
    }

    public function show($id)
    {
        $campaign = CampaignsModel::with(['status', 'department', 'agreement'])->findOrFail($id);
        return Inertia::render('Campaigns/Show', ['campaign' => $campaign]);
    }

    public function edit($id)
    {
        $campaign = CampaignsModel::findOrFail($id);
        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'statuses' => StatusModel::all(),
            'departments' => DepartmentsModel::all(),
            'agreements' => AgreementsModel::all(),
        ]);
    }

    // --- AQUÍ TAMBIÉN CAMBIA ---
    public function update(SaveCampaignRequest $request, $id)
    {
        $campaign = CampaignsModel::findOrFail($id);

        // 1. Obtener datos validados
        $data = $request->validated();

        // 2. Formateo de fechas para SQL Server
        $data['start_at'] = Carbon::parse($data['start_at'])->format('Y-m-d H:i:s');
        $data['end_at'] = Carbon::parse($data['end_at'])->format('Y-m-d H:i:s');

        // 3. Auditoría
        $data['updated_by'] = Auth::id();

        // 4. Actualizar
        $campaign->update($data);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy($id)
    {
        $campaign = CampaignsModel::findOrFail($id);
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña eliminada.');
    }
}
