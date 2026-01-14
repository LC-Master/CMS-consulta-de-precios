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
        $campaigns = Campaign::whereHas(
            'centers',
            fn($q) =>
            $q->where('centers.id', $center->getKey())
        )->whereHas(
                'status',
                fn($q) =>
                $q->where('status', CampaignStatus::ACTIVE->value)
            )
            ->with(['status', 'department', 'agreement', 'media'])
            ->get();
        $snapshot = [
            'center_id' => $center->getKey(),
            'campaigns' => [],
        ];

        foreach ($campaigns as $c) {
            $snapshot['campaigns'][] = [
                'id' => $c->getKey(),
                'title' => $c->title,
                'status' => $c->status?->status,
                'department' => $c->department?->name,
                'agreement' => $c->agreement?->name,
                'start_at' => $c->start_at?->toIso8601String(),
                'end_at' => $c->end_at?->toIso8601String(),
                'media' => $c->getRelation('media')
                    ->map(fn($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                        'duration_seconds' => $m->duration_seconds,
                        'slot' => $m->pivot->slot,
                        'position' => $m->pivot->position,
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
        $sorted = collect($data)->map(function ($item) {
            $mediaCollection = collect($item['media']);

            $mediaCollection = collect($item['media']);

            $normalizedMedia = $mediaCollection->map(function ($m) {
                $m['position'] = isset($m['position']) ? (int) $m['position'] : 0;
                $m['duration_seconds'] = isset($m['duration_seconds']) ? (int) $m['duration_seconds'] : 0;
                return $m;
            });

            $item['slots'] = [
                'am' => $normalizedMedia->where('slot', 'am')
                    ->sortBy('position')
                    ->values()
                    ->map(function ($m) {
                        unset($m['slot']);
                        return $m;
                    })
                    ->all(),

                'pm' => $normalizedMedia->where('slot', 'pm')
                    ->sortBy('position')
                    ->values()
                    ->map(function ($m) {
                        unset($m['slot']);
                        return $m;
                    })
                    ->all(),
            ];

            unset($item['media']);

            return $item;
        });

        return $sorted->all();
    }
}
