<?php

namespace App\Actions\Campaign;

use App\Enums\Schedules;
use App\Models\Campaign;
use App\Models\Status;
use App\Models\Center;
use App\Enums\CampaignStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateCampaignAction
{
    public function execute(array $data): Campaign
    {
        $draftStatus = Status::where('status', CampaignStatus::DRAFT->value)->first();
        if (!$draftStatus) {
            Log::critical('Integrity Error: Default campaign status (DRAFT) missing.', ['required_status' => CampaignStatus::DRAFT->value]);
            throw new \RuntimeException('Error de configuración del sistema: Estado inicial no encontrado.');
        }

        return DB::transaction(function () use ($data, $draftStatus) {

            $campaign = Campaign::create(attributes: [
                ...$data,
                'created_by' => Auth::id(),
                'status_id' => $draftStatus->getKey(),
            ]);

            if (!empty($data['centers'])) {
                $special = Center::where('code', 'CTR-0001')->first();
                $centerIds = ($special && \in_array($special->getKey(), $data['centers'], true))
                    ? Center::pluck('id')->toArray()
                    : $data['centers'];

                $campaign->centers()->attach($centerIds);
            }

            foreach (['am_media' => Schedules::AM, 'pm_media' => Schedules::PM] as $key => $schedule) {
                if (!empty($data[$key])) {
                    $items = collect($data[$key])->map(fn($id, $pos) => [
                        'media_id' => $id,
                        'slot' => $schedule->value,
                        'position' => $pos + 1,
                    ])->all();

                    $campaign->timeLineItems()->createMany($items);
                }
            }

            return $campaign;
        });
    }
}
