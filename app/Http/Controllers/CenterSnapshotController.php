<?php

namespace App\Http\Controllers;

use App\Actions\dto\CampaignSnapshotDTO;
use App\Models\CenterSnapshot;
use Illuminate\Http\Request;

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
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

}
