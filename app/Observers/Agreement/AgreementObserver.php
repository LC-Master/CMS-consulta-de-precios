<?php

namespace App\Observers\Agreement;

use App\DTOs\RecordActivityLogs\AgreementJobDTO;
use App\Jobs\RecordActivityJob;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Models\Agreement;

/**
 * Summary of AgreementObserver
 * @author Francisco Rojas 
 * @abstract Observador para el modelo Agreement que registra actividades en logs.
 * @version 1.0
 * @since 2026-1-28 
 */
class AgreementObserver
{
    /**
     * Summary of created
     *@abstract Escucha el evento de creación y registra la creación en los logs.
     * @param Agreement $agreement
     * @return void
     */
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
    /**
     * Summary of updated
     * @abstract Escucha el evento de actualización y registra los cambios en los logs.
     * @param Agreement $agreement
     * @return void
     */
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
     * @abstract Escucha el evento de eliminación (SoftDelete) y registra la eliminación en los logs.
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
     * Método privado para no repetir el dispatch
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
