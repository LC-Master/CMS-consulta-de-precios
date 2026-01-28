<?php

namespace App\Observers\Campaign;

use App\Models\Campaign;
use App\DTOs\RecordActivityLogs\CampaignJobDTO;
use App\Jobs\RecordActivityJob;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;


/**
 * Summary of CampaignObserver
 * @author Francisco Rojas 
 * @abstract Observador para el modelo Campaign que registra actividades en logs.
 * @version 1.0
 * @since 2026-1-28 
 */
class CampaignObserver
{
    /**
     * Escucha el evento de creación.
     */
    public function created(Campaign $campaign): void
    {
        $dto = new CampaignJobDTO(
            (string) $campaign->getKey(),
            $campaign->getAttribute('title'),
            $campaign->toArray(),
        );

        $this->dispatchLog($dto, LogActionEnum::CREATED, "Campaña creada: {$campaign->getAttribute('title')}");
    }

    /**
     * Escucha el evento de actualización.
     */
    public function updated(Campaign $campaign): void
    {
        if ($campaign->wasChanged()) {
            $changes = [
                'before' => array_intersect_key($campaign->getOriginal(), $campaign->getChanges()),
                'after' => $campaign->getChanges(),
            ];

            $dto = new CampaignJobDTO(
                (string) $campaign->getKey(),
                $campaign->getAttribute('title'),
                $changes
            );

            $this->dispatchLog($dto, LogActionEnum::UPDATED, "Campaña actualizada");
        }
    }

    /**
     * Escucha el evento de eliminación (SoftDelete).
     */
    public function deleted(Campaign $campaign): void
    {
        $dto = new CampaignJobDTO(
            (string) $campaign->getKey(),
            $campaign->getAttribute('title'),
            ['deleted_at' => $campaign->getAttribute('deleted_at')->toDateTimeString()]
        );

        $this->dispatchLog($dto, LogActionEnum::DELETED, "Campaña enviada a papelera");
    }
    /**
     * Escucha el evento de restauración (SoftDelete).
     */
    public function restored(Campaign $campaign): void
    {
        $dto = new CampaignJobDTO(
            (string) $campaign->getKey(),
            $campaign->getAttribute('title'),
            ['deleted_at' => null]
        );

        $this->dispatchLog($dto, LogActionEnum::RESTORED, "Campaña restaurada desde papelera");
    }
    /**
     * Método privado para no repetir el dispatch
     */
    private function dispatchLog(CampaignJobDTO $dto, LogActionEnum $action, string $message): void
    {
        RecordActivityJob::dispatch(
            dto: $dto,
            action: $action,
            level: LogLevelEnum::INFO,
            message: $message,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            causer: auth()->user()
        );
    }
}