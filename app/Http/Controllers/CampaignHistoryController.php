<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Status;
use App\Enums\CampaignStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CampaignHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::withTrashed()->with([
            'status:id,status',
            'department:id,name',
            'agreement:id,name'
        ]);

        $query->where(function ($q) {
            $q->whereHas('status', fn($sq) =>
            $sq->where('status', CampaignStatus::FINISHED->value)->orWhere('status', CampaignStatus::CANCELLED->value))
                ->orWhereNotNull('deleted_at');
        });

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->input('search')}%");
        }

        if ($request->filled('ended_at')) {
            $query->whereDate('end_at', '<=', $request->input('ended_at'));
        }

        if ($request->filled('started_at')) {
            $query->whereDate('start_at', '>=', $request->input('started_at'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'deleted') {
                $query->onlyTrashed();
            } else {
                $query->where('status_id', $status);
            }
        }

        $statuses = Status::where('status', '=', value: CampaignStatus::FINISHED->value)->get(['id', 'status']);

        return Inertia::render('CampaignHistory/Index', [
            'campaigns' => Inertia::scroll($query->latest()->paginate(10)->withQueryString()),
            'filters' => $request->only(['search', 'status', 'ended_at', 'started_at']),
            'statuses' => $statuses
        ]);
    }
    public function show(Campaign $campaign)
    {
        $campaign->load([
            'status:id,status',
            'department:id,name',
            'agreement' => fn($query) => $query->withTrashed()->select('id', 'name', 'deleted_at'),
            'centers:id,name,code',
            'media:id,name,mime_type,duration_seconds',
        ])->makeHidden(['status_id', 'department_id', 'agreement_id', 'updated_by', 'user_id', 'updated_at']);

        return Inertia::render('CampaignHistory/Show', [
            'campaign' => $campaign,
        ]);
    }
    public function restore(Campaign $campaign)
    {
        $campaign->restore();

        return back()->with('success', 'Campaña restaurada al panel de campañas.');
    }

    public function clone(Campaign $campaign)
    {

        $newCampaign = $campaign->replicate();
        $newCampaign->setAttribute('deleted_at', null);
        $newCampaign->setAttribute('title', "Copia de {$campaign->getAttribute('title')}");
        $newCampaign->setAttribute('status_id', Status::where('status', CampaignStatus::DRAFT->value)->first()->getKey());
        $newCampaign->setAttribute('start_at', now()->addDay()->startOfDay());
        $newCampaign->setAttribute('end_at', now()->addDays(8)->endOfDay());
        $newCampaign->save();

        $newCampaign->centers()->attach($campaign->centers->pluck('id'));

        $campaign->load('media');

        foreach ($campaign->media as $mediaItem) {
            $newCampaign->media()->attach($mediaItem->id, [
                'id' => (string) Str::uuid(),
                'slot' => $mediaItem->pivot->slot,
                'position' => $mediaItem->pivot->position,
            ]);
        }

        return to_route("campaign.edit", ['campaign' => $newCampaign->getKey()])
            ->with('success', 'Campaña clonada. Revisa las fechas.');
    }

    public function calendar()
    {
        try {
            $now = now();

            $campaigns = Campaign::with([
                'status:id,status',
                'department:id,name',
                'centers:id,name',
                'agreement' => fn($query) => $query->withTrashed()->select('id', 'name', 'deleted_at'),
            ])
                ->whereYear('start_at', $now->year)
                ->whereHas('status', function ($sq) {
                    $sq->where('status', CampaignStatus::ACTIVE->value);
                })
                ->get()
                ->map(function ($c) {
                    $start = $c->getAttribute('start_at') instanceof Carbon ? $c->getAttribute('start_at') : Carbon::parse($c->getAttribute('start_at'));
                    $end = $c->getAttribute('end_at') instanceof Carbon ? $c->getAttribute('end_at') : Carbon::parse($c->getAttribute('end_at') ?? $c->getAttribute('start_at'));

                    return [
                        'id' => $c->getKey(),
                        'title' => $c->getAttribute('title'),
                        'start' => $start->toIso8601String(),
                        'end' => $end->addDay()->toIso8601String(),
                        'color' => '#3B82F6',
                        'extendedProps' => [
                            'department' => $c->department->name ?? 'N/A',
                            'agreement' => $c->agreement->name ?? 'N/A',
                            'centers' => $c->centers->pluck('name')->toArray(),
                        ]
                    ];
                });

            return Inertia::render('CampaignHistory/Calendar', [
                'campaigns' => $campaigns
            ]);
        } catch (\Exception $e) {
            Log::error("Error en Calendario: " . $e->getMessage());
            return back()->with('error', 'Error interno: ' . $e->getMessage());
        }
    }
}
