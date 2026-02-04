<?php

namespace App\Actions\Campaign;

use App\Enums\Schedules;
use App\Models\Campaign;
use App\Models\Status;
use App\Enums\CampaignStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateCampaignAction
{
    public function execute(Request $data): Campaign
    {
        $draftStatus = Status::where('status', CampaignStatus::DRAFT->value)->first();
        if (!$draftStatus) {
            Log::critical('Integrity Error: Default campaign status (DRAFT) missing.', ['required_status' => CampaignStatus::DRAFT->value]);
            throw new \RuntimeException('Error de configuración del sistema: Estado inicial no encontrado.');
        }

        return DB::transaction(function () use ($data, $draftStatus) {

            $campaign = Campaign::create([
                ...$data->all(),
                'status_id' => $draftStatus->getKey(),
            ]);

            if (!empty($data->input('agreements'))) {
                $campaign->agreements()->attach($data->input('agreements'));
            }


            if (!empty($data->input('stores'))) {
                $campaign->stores()->attach($data->input('stores'));
            }

            $timelineItems = [];
            $now = now();

            foreach (['am_media' => Schedules::AM, 'pm_media' => Schedules::PM] as $key => $schedule) {
                if (!empty($data->input($key))) {
                    foreach ($data->input($key) as $position => $mediaId) {
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
