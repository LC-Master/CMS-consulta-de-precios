<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CreateCampaignAction;
use App\Actions\Campaign\UpdateCampaignAction;
use App\Enums\CampaignStatus;
use App\Http\Requests\Campaigns\StoreCampaignRequest;
use App\Http\Requests\Campaigns\UpdateCampaignRequest;
use App\Models\Agreement;
use App\Models\Campaign;
use App\Models\Center;
use App\Models\Department;
use App\Models\Media;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with(['status']);

        $query->whereHas('status', function ($q) {
            $q->where('status', '!=', CampaignStatus::FINISHED->value)->where('status', '!=', CampaignStatus::CANCELLED->value);
        });

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->input('search')}%");
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->input('status'));
        }

        $statuses = Status::where('status', '!=', CampaignStatus::FINISHED->value)->where('status', '!=', CampaignStatus::CANCELLED->value)->get(['id', 'status']);
        return Inertia::render('Campaign/Index', [
            'campaigns' => Inertia::scroll(value: $query->latest()->paginate(10)->withQueryString()),
            'filters' => $request->only(['search', 'status']),
            'statuses' => $statuses
        ]);
    }

    public function create()
    {
        return Inertia::render('Campaign/Create', [
            'media' => Media::with([
                'thumbnail' => function ($query) {
                    $query->select('id', 'media_id');
                }
            ])
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get(['id', 'name', 'mime_type']),

            'centers' => Center::get(['id', 'code', 'name']),
            'departments' => Department::get(['id', 'name']),
            'agreements' => Agreement::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function store(StoreCampaignRequest $request, CreateCampaignAction $createCampaignAction): RedirectResponse
    {
        try {
            $request->validated();
            $campaign = $createCampaignAction->execute($request);
            Auth::user()?->notify(new \App\Notifications\Campaigns\CampaignCreatedNotification(campaign: $campaign));
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
            // 'agreements' => fn($query) => $query->withTrashed()->select('id', 'name'),
            'centers:id,code,name',
            'media:id,name,mime_type,duration_seconds',
        ]);

        $campaign->makeHidden(['status_id', 'department_id', 'agreement_id']);

        return Inertia::render('Campaign/Show', [
            'campaign' => $campaign
        ]);
    }

    public function edit(Campaign $campaign)
    {
        $campaign->load([
            'media:id,name,mime_type,duration_seconds',
            'media.thumbnail:id,path,media_id',
            'centers:id,code,name',
            'agreements' => fn($query) => $query->withTrashed()->select('id', 'name'),
        ]);

        $campaign->setRelation('media', $campaign->getRelation('media')->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'mime_type' => $item->mime_type,
            'duration_seconds' => $item->duration_seconds,
            'slot' => $item->pivot->slot,
            'position' => $item->pivot->position,
            'thumbnail' => [
                'id' => $item->thumbnail?->id,
            ],
        ]));

        $campaign->makeHidden(['updated_by', 'created_by', 'status_id']);

        return Inertia::render('Campaign/Edit', [
            'campaign' => $campaign,
            'statuses' => Status::all(['id', 'status']),
            'departments' => Department::all(['id', 'name']),
            'agreements' => Agreement::where('is_active', true)->get(['id', 'name']),
            'media' => Media::with('thumbnail:id,media_id')
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get(['id', 'name', 'mime_type']),
            'centers' => Center::all(['id', 'code', 'name']),
        ]);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign, UpdateCampaignAction $updateCampaignAction): RedirectResponse
    {
        try {
            $request->validated();

            $updateCampaignAction->execute($request, $campaign);

            return to_route('campaign.index')
                ->with('success', 'Campaña actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error updating campaign: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al actualizar la campaña. Por favor, intente nuevamente.');
        }
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        $campaign->load([
            'department',
            'updatedBy',
            'status',
            'user',
            'agreement' => fn($q) => $q->withTrashed()

        ]);

        try {

            if ($campaign->user) {
                $campaign->user->notify(new \App\Notifications\Campaigns\CampaignDeletedNotification($campaign));
            }

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
            if ($admins->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\Campaigns\CampaignDeletedNotification($campaign));
            }

            $referer = request()->headers->get('referer');
            if ($referer && str_contains($referer, '/history/campaigns')) {
                return back()->with('success', 'Campaña Inhabilitada permanentemente.');
            }

            return to_route('campaign.index')
                ->with('success', 'Campaña Inhabilitada.');
        } catch (\Throwable $e) {
            Log::error('Error deleting campaign: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->with('error', 'Ocurrió un error inesperado al Inhabilitar la campaña. Por favor, intente nuevamente.');
        }
    }
    public function activate(Campaign $campaign)
    {
        try {
            $activeStatus = Status::where('status', CampaignStatus::ACTIVE->value)->first();
            $campaign->setAttribute('status_id', $activeStatus->getKey());
            $campaign->save();
            $campaign->user->notify(new \App\Notifications\Campaigns\CampaignPublishedNotification(campaign: $campaign));
            return back()
                ->with('success', 'Campaña activada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error activating campaign: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al activar la campaña. Por favor, intente nuevamente.');
        }
    }
    public function cancel(Campaign $campaign)
    {
        try {
            $cancelledStatus = Status::where('status', CampaignStatus::CANCELLED->value)->firstOrFail();

            if (!$cancelledStatus->getKey() === $campaign->getAttribute('status_id')) {
                return back()->with('success', "Estatus de " . CampaignStatus::FINISHED->value . " ya asignado a la campaña.");
            }

            $campaign->update(['status_id' => $cancelledStatus->getKey()]);
            $campaign->user->notify(new \App\Notifications\Campaigns\CampaignCancelledNotification(campaign: $campaign));

            return redirect()->route('campaign.index')->with('success', 'Campaña cancelada.');
        } catch (\Throwable $e) {
            Log::error('Error cancelling campaign: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            return back()
                ->with('error', 'Ocurrió un error inesperado al cancelar la campaña. Por favor, intente nuevamente.');
        }
    }
}
