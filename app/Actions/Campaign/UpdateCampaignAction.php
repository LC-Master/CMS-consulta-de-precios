<?php

namespace App\Actions\Campaign;

use Illuminate\Http\Request;
use DB;
use App\Models\Campaign;
use App\Enums\Schedules;

class UpdateCampaignAction
{
    /**
     * Create a new class instance.
     */
    public function execute(Request $request, Campaign $campaign): Campaign
    {
        return DB::transaction(function () use ($request, $campaign) {
            $campaign->update($request->only([
                'title',
                'start_at',
                'end_at',
                'department_id',
                'agreement_id',
            ]));

            $campaign->centers()->sync($request->input('centers', []));

            $this->updateTimeline($campaign, $request->input('am_media', []), Schedules::AM);
            $this->updateTimeline($campaign, $request->input('pm_media', []), Schedules::PM);

            return $campaign;
        });
    }

    /**
     * Función auxiliar para evitar repetir código y optimizar inserts
     */
    private function updateTimeline(Campaign $campaign, array $mediaIds, Schedules $slot): void
    {

        $campaign->timeLineItems()->where('slot', $slot->value)->delete();

        if (empty($mediaIds))
            return;

        $items = collect($mediaIds)
            ->values()
            ->map(function ($mediaId, $index) use ($slot, $campaign) {
                return [
                    'media_id' => $mediaId,
                    'slot' => $slot->value,
                    'position' => $index + 1,
                    'campaign_id' => $campaign->id,
                ];
            })->toArray();

        $campaign->timeLineItems()->createMany($items);
    }
}
