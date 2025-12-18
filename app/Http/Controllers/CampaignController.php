<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CreateCampaignAction;
use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Campaigns\UpdateCampaignRequest;
use App\Models\Agreement;
use App\Models\Campaign;
use App\Models\Center;
use App\Models\Department;
use App\Models\Media;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with(['status', 'department', 'agreement']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        return Inertia::render('Campaign/Index', [
            'campaigns' => Inertia::scroll(fn () => $query->latest()->paginate()),
            'filters' => $request->only(['search', 'status']),
            'statuses' => fn () => Status::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Campaign/Create', [
            'media' => Media::select('id', 'name', 'mime_type')
                ->with(['thumbnails' => function ($query) {
                    $query->select('id', 'media_id');
                }])
                ->get(),
            'centers' => Center::select('id', 'code', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'agreements' => Agreement::select('id', 'name')->get(),
        ]);
    }

    public function store(StoreCampaignRequest $request, CreateCampaignAction $createCampaignAction): RedirectResponse
    {
        try {

            $createCampaignAction->execute($request->validated());
            dd($request);
            return to_route('campaign.index')
                ->with('success', 'Campaña creada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error creating campaign: '.$e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear la campaña. Por favor, intente nuevamente.');
        }
    }

    public function show(Campaign $campaign)
    {
        $campaign->with(['status', 'department', 'agreement']);

        return Inertia::render('Campaign/Show', ['campaign' => $campaign]);
    }

    public function edit(Campaign $campaign)
    {
        return Inertia::render('Campaign/Edit', [
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

        return to_route('campaign.index')
            ->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return to_route('campaign.index')
            ->with('success', 'Campaña eliminada.');
    }
}
