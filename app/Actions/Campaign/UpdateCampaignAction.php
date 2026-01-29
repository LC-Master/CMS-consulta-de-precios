<?php

namespace App\Actions\Campaign;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Enums\Schedules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class UpdateCampaignAction
{
    /**
     * Create a new class instance.
     */
    public function execute(Request $request, Campaign $campaign): Campaign
    {
        return DB::transaction(function () use ($request, $campaign) {

            $campaign->old_media_files = $campaign->timeLineItems()
                ->with('media')
                ->get()
                ->map(fn($item) => $item->media->name ?? 'archivo-desconocido')
                ->toArray();

            $campaign->old_agreements = $campaign->agreements->pluck('name')->toArray();
            $campaign->old_centers = $campaign->centers->pluck('name')->toArray();

            $campaign->update($request->only([
                'title',
                'start_at',
                'end_at',
                'department_id',
            ]));

            $campaign->agreements()->sync($request->input('agreements', []));
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
            ->map(fn($mediaId, $index) => [
                'media_id' => $mediaId,
                'slot' => $slot->value,
                'position' => $index + 1,
                'campaign_id' => $campaign->getKey(),
            ])->toArray();

        $campaign->timeLineItems()->createMany($items);
    }
}
