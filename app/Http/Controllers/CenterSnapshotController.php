<?php

namespace App\Http\Controllers;

use App\Actions\dto\CampaignSnapshotDTO;
use App\Enums\SyncStatusEnum;
use App\Models\CenterSnapshot;
use App\Models\StoreSyncState;
use Illuminate\Http\Request;
use App\Events\StoreSyncUpdated;

class CenterSnapshotController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(CampaignSnapshotDTO $campaignSnapshotDTO, Request $request)
    {
        try {
            $campaignSnapshotDTO = $campaignSnapshotDTO->execute($request->user());
            if (!empty($campaignSnapshotDTO['campaigns'])) {
                $campaignSnapshotDTO['campaigns'] = CampaignSnapshotDTO::normalize($campaignSnapshotDTO['campaigns']);
            }

            $snapShot = CenterSnapshot::updateOrCreate(
                [
                    'store_id' => $request->user()->getKey(),
                ],
                [
                    'snapshot_json' => $campaignSnapshotDTO,
                ]
            );

            StoreSyncState::updateOrCreate(
                [
                    'store_id' => $request->user()->getKey(),
                ],
                [
                    'sync_status' => SyncStatusEnum::SYNCING->value,
                    'sync_started_at' => now(),
                    'sync_ended_at' => null,
                    'last_reported_at' => now(),
                ]
            );

            StoreSyncUpdated::dispatch('Sincronización iniciada');

            return response()->json([
                'meta' => [
                    'api_version' => config('dto.dto_version'),
                    'version' => $snapShot->getAttribute('version_hash'),
                    'generated_at' => now()->toIso8601String(),
                ],
                'data' => $snapShot->snapshot_json,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate snapshot',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function health(Request $request)
    {
        StoreSyncState::updateOrCreate(
            [
                'store_id' => $request->user()->getKey(),
            ],
            [
                'last_reported_at' => now(),
            ]
        );

        StoreSyncUpdated::dispatch('Health check recibida');

        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

}
