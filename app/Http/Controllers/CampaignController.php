<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CreateCampaignAction;
use App\Enums\CampaignStatus;
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
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        return Inertia::render('Campaign/Index', [
            'campaigns' => Inertia::scroll(fn() => $query->latest()->paginate()),
            'filters' => $request->only(['search', 'status']),
            'statuses' => fn() => Status::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Campaign/Create', [
            'media' => Media::select('id', 'name', 'mime_type')
                ->with([
                    'thumbnails' => function ($query) {
                        $query->select('id', 'media_id');
                    }
                ])
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
            return to_route('campaign.index')
                ->with('success', 'Campaña creada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error creating campaign: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear la campaña. Por favor, intente nuevamente.');
        }
    }

    public function show(Campaign $campaign)
    {
        $campaign->load([
            'status',
            'department:id,name',
            'agreement:id,name',
            'media' => function ($query) {
                $query->select('media.id', 'media.name', 'media.mime_type', 'media.duration_seconds');
            }
        ]);
        $campaign->makeHidden(['status_id', 'department_id', 'agreement_id']);
        return Inertia::render('Campaign/Show', [
            'campaign' => $campaign
        ]);
    }

    public function edit(Campaign $campaign)
    {
        $campaign->load([
            'media' => function ($query) {
                $query->select('media.id', 'media.name', 'media.mime_type', 'media.duration_seconds')
                    ->with('thumbnails:id,path,media_id')
                    ->withPivot('slot', 'position')
                    ->orderBy('time_line_items.position');
            }
        ]);

        $flattenedMedia = $campaign->media->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'mime_type' => $item->mime_type,
            'duration_seconds' => $item->duration_seconds,
            'slot' => $item->pivot->slot,
            'position' => $item->pivot->position,
            'thumbnail_id' => $item->thumbnails?->id
        ]);

        $campaign->setRelation('media', $flattenedMedia);
        $campaign->makeHidden(['updated_by']);
        return Inertia::render('Campaign/Edit', [
            'campaign' => $campaign,
            'statuses' => Status::all(['id', 'status']),
            'departments' => Department::all(['id', 'name']),
            'agreements' => Agreement::all(['id', 'name']),
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
    public function activate(Campaign $campaign)
    {
        try {
            $activeStatus = Status::where('status', 'Activa')->first();
            $campaign->status_id = $activeStatus->id;
            $campaign->save();

            return back()
                ->with('success', 'Campaña activada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error activating campaign: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al activar la campaña. Por favor, intente nuevamente.');
        }
    }
    public function finish(Campaign $campaign)
    {
        $finishedStatus = Status::where('status', CampaignStatus::FINISHED->value)->first();
        $campaign->update(['status_id' => $finishedStatus->id]);

        return redirect()->route('campaign.index')->with('success', 'Campaña finalizada.');
    }
}
