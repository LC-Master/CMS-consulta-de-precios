<?php

namespace App\Actions\dto;

use App\Enums\CampaignStatus;
use App\Models\Center;
use App\Models\Campaign;

class CampaignSnapshotDTO
{
    /**
     * Create a new class instance.
     */
    public static function execute(Center $center)
    {
        $campaigns = Campaign::whereHas('centers', fn($q) => $q->where('center_id', $center->id))
            ->whereHas('status', fn($q) => $q->where('status', CampaignStatus::ACTIVE->value))
            ->with(['status', 'department', 'agreement', 'media.thumbnails'])
            ->get();

        $snapshot = [
            'center_id' => $center->id,
            'campaigns' => [],
        ];

        foreach ($campaigns as $c) {
            $snapshot['campaigns'][] = [
                'id' => $c->id,
                'title' => $c->title,
                'status' => $c->status?->status,
                'department' => $c->department?->name,
                'agreement' => $c->agreement?->name,
                'start_at' => $c->start_at?->toIso8601String(),
                'end_at' => $c->end_at?->toIso8601String(),
                'media' => $c->media
                    ->map(fn($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'path' => $m->path,
                        'duration_seconds' => $m->duration_seconds,
                        'slot' => $m->pivot->slot,
                        'position' => $m->pivot->position,
                        'thumbnails' => $m->thumbnails ? [
                            'id' => $m->thumbnails->id,
                            'path' => $m->thumbnails->path,
                        ] : null,
                    ])
                    ->sortBy('pivot.position')
                    ->values()
                    ->all(),
            ];
        }

        return $snapshot;
    }

    public static function normalize(array $data): array
    {
        sort($data['campaigns']);

        foreach ($data['campaigns'] as &$campaign) {
            sort($campaign['slots']['am']);
            sort($campaign['slots']['pm']);
        }

        return $data;
    }
}
