<?php

namespace App\Observers\Agreement;

use App\DTOs\RecordActivityLogs\AgreementJobDTO;
use App\Jobs\RecordActivityJob;
use App\DTOs\RecordActivityLogs\CampaignJobDTO;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Models\Agreement;

class AgreementObserver
{
    public function created(Agreement $agreement): void
    {
        $dto = new AgreementJobDTO(
            id: $agreement->getKey(),
            payload: $agreement->toArray()
        );

        $this->dispatchLog(
            dto: $dto,
            action: LogActionEnum::CREATED,
            message: 'El acuerdo ha sido creado'
        );

    }
    public function updated(Agreement $agreement): void
    {
        if ($agreement->wasChanged()) {
            $changes = [
                'before' => array_intersect_key($agreement->getOriginal(), $agreement->getChanges()),
                'after' => $agreement->getChanges(),
            ];

            $dto = new AgreementJobDTO(
                id: $agreement->getKey(),
                payload: $agreement->toArray(),
                changes: $changes
            );

            $this->dispatchLog($dto, LogActionEnum::UPDATED, "Acuerdo comercial actualizado");
        }
    }
    /**
     * Summary of deleted
     * @abstract Escucha el evento de eliminación (SoftDelete).
     * @param Agreement $agreement
     * @return void
     */
    public function deleted(Agreement $agreement): void
    {
        $dto = new AgreementJobDTO(
            id: $agreement->getKey(),
            payload: $agreement->toArray(),
            changes: ['deleted_at' => $agreement->getAttribute('deleted_at')->toDateTimeString()]
        );

        $this->dispatchLog($dto, LogActionEnum::DELETED, "Acuerdo comercial eliminado");
    }
    /**
     * Despacha un trabajo de registro de actividad.
     * 
     * Método privado que centraliza el dispatch del RecordActivityJob
     * para evitar repetición de código en los métodos del observador.
     * 
     * @param AgreementJobDTO $dto Datos del acuerdo a registrar
     * @param LogActionEnum $action Acción realizada (CREATED, UPDATED, DELETED)
     * @param string $message Mensaje descriptivo de la acción
     * @return void
     */
    private function dispatchLog(AgreementJobDTO $dto, LogActionEnum $action, string $message): void
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
