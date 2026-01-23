<?php

namespace App\Actions\Campaign;

use App\Enums\Schedules;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\Center;
use App\Enums\CampaignStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateCampaignAction
{
    public function execute(array $data): Campaign
    {
        $draftStatus = Status::where('status', CampaignStatus::DRAFT->value)->first();
        if (!$draftStatus) {
            Log::critical('Integrity Error: Default campaign status (DRAFT) missing.', ['required_status' => CampaignStatus::DRAFT->value]);
            throw new \RuntimeException('Error de configuración del sistema: Estado inicial no encontrado.');
        }

        $inputCenterIds = $data['centers'] ?? [];
        $finalCenterIds = $inputCenterIds;

        if (!empty($inputCenterIds)) {
            $specialCenterId = Center::where('code', 'CTR-0001')->value('id');

            if ($specialCenterId && \in_array($specialCenterId, $inputCenterIds)) {
                $finalCenterIds = Center::pluck('id')->toArray();
            }
        }

        return DB::transaction(function () use ($data, $draftStatus, $finalCenterIds) {

            $campaign = Campaign::create([
                ...$data,
                'status_id' => $draftStatus->getKey(),
            ]);

            if (!empty($finalCenterIds)) {
                $campaign->centers()->attach($finalCenterIds);
            }

            $timelineItems = [];
            $now = now();

            foreach (['am_media' => Schedules::AM, 'pm_media' => Schedules::PM] as $key => $schedule) {
                if (!empty($data[$key])) {
                    foreach ($data[$key] as $position => $mediaId) {
                        $timelineItems[] = [
                            'id' => Str::uuid()->toString(),
                            'campaign_id' => $campaign->getKey(),
                            'media_id' => $mediaId,
                            'slot' => $schedule->value,
                            'position' => $position + 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            if (!empty($timelineItems)) {
                DB::table('time_line_items')->insert($timelineItems);
            }
            return $campaign;
        });
    }
}