<?php

namespace App\Actions\Campaign;

use App\Enums\Schedules;
use App\Models\Campaign;
use App\Models\Status;
use App\Enums\CampaignStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 

class CreateCampaignAction
{
    public function execute(array $data): Campaign
    {
        return DB::transaction(function () use ($data) {

            $draftStatus = Status::where('status', CampaignStatus::DRAFT->value)->first();

            if (! $draftStatus) {
                Log::critical('Integrity Error: Default campaign status (DRAFT) missing.', ['required_status' => CampaignStatus::DRAFT->value]);
                throw new \RuntimeException('Error de configuración del sistema: Estado inicial no encontrado.');
            }

            $campaign = Campaign::create(array_merge($data, [
                'created_by' => Auth::id(),
                'status_id' => $draftStatus->id,
            ]));

            if (! empty($data['centers'])) {
                $campaign->centers()->attach($data['centers']);
            }
            if (! empty($data['am_media'])){
                foreach ($data['am_media'] as $position => $mediaId) {
                    $campaign->timeLineItems()->create([
                        'media_id' => $mediaId,
                        'slot' => Schedules::AM->value,
                        'position' => $position + 1,
                    ]);
                }
            }
            if (! empty($data['pm_media'])){
                foreach ($data['pm_media'] as $position => $mediaId) {
                    $campaign->timeLineItems()->create([
                        'media_id' => $mediaId,
                        'slot' => Schedules::PM->value,
                        'position' => $position + 1,
                    ]);
                }
            }


            return $campaign;
        });
    }
}
