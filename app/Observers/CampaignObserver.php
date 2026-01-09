<?php

namespace App\Observers;

use App\Enums\Log\LogLevelEnum;
use App\Enums\Log\LogActionEnum;
use App\Models\Campaign;
use App\Jobs\RecordCampaignActivityJob;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class CampaignObserver
{
    /**
     * Procesa los cambios para el formato OLD/NEW
     */
    private function getFormattedChanges(Campaign $campaign): array
    {
        $changes = [];
        foreach ($campaign->getChanges() as $key => $newValue) {
            if (\in_array($key, ['updated_at', 'created_at', 'deleted_at'])) continue;

            $changes[$key] = [
                'old' => $campaign->getOriginal($key),
                'new' => $newValue
            ];
        }
        return $changes;
    }

    public function created(Campaign $campaign): void
    {
        RecordCampaignActivityJob::dispatch(
            Auth::id() ?? 0,
            $campaign,
            LogActionEnum::CREATED,
            LogLevelEnum::INFO, 
            "Se creó la campaña: {$campaign->title}",
            [
                'title' => $campaign->title,
                'changes' => $campaign->getAttributes()
            ]
        );
    }

    public function updated(Campaign $campaign): void
    {
        $changes = $this->getFormattedChanges($campaign);

        if (empty($changes)) return;

        RecordCampaignActivityJob::dispatch(
            Auth::id() ?? 0,
            $campaign,
            LogActionEnum::UPDATED,
            LogLevelEnum::INFO,
            "Se actualizó la campaña: {$campaign->title}",
            [
                'title' => $campaign->title,
                'changes' => $changes 
            ]
        );
    }

    public function deleted(Campaign $campaign): void
    {
        RecordCampaignActivityJob::dispatch(
            Auth::id() ?? 0,
            $campaign,
            LogActionEnum::DELETED,
            LogLevelEnum::DANGER,
            "Campaña '{$campaign->title}' eliminada.",
            ['title' => $campaign->title]
        );
    }

    public function restored(Campaign $campaign): void
    {
        RecordCampaignActivityJob::dispatch(
            Auth::id() ?? 0,
            $campaign,
            LogActionEnum::UPDATED, 
            LogLevelEnum::INFO,
            "Campaña '{$campaign->title}' restaurada.",
            ['title' => $campaign->title]
        );
    }
}