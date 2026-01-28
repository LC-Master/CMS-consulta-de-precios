<?php

namespace App\Observers\User;

use App\DTOs\RecordActivityLogs\UserJobDTO;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Jobs\RecordActivityJob;

/**
 * Summary of UserObserver
 * @author Francisco Rojas
 * @abstract Observer para el modelo User, utilizado para capturar los logs de aplicación
 * @version 1.0.0
 * @since 2026-01-28
 */
class UserObserver
{
    public function created(\App\Models\User $user): void
    {
        $dto = new UserJobDTO(
            id: $user->getKey(),
            name: $user->name,
            email: $user->email,
            payload: $user->toArray()
        );

        $this->dispatchLog($dto, LogActionEnum::CREATED, 'El usuario ha sido creado');
    }
    
    
    public function updated(\App\Models\User $user): void
    {
        if ($user->wasChanged()) {
            $excludedKeys = ['password', 'remember_token', 'status', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'];

            $changes = [
                'before' => array_intersect_key(
                    array_diff_key($user->getOriginal(), array_flip($excludedKeys)),
                    $user->getChanges()
                ),
                'after' => array_diff_key($user->getChanges(), array_flip($excludedKeys)),
            ];

            $dto = new UserJobDTO(
                id: $user->getKey(),
                name: $user->name,
                email: $user->email,
                payload: array_diff_key($user->getOriginal(), array_flip($excludedKeys)),
                changes: $changes
            );

            $this->dispatchLog($dto, LogActionEnum::UPDATED, "El usuario ha sido actualizado");
        }
    }
    public function deleted(\App\Models\User $user): void
    {
        $dto = new UserJobDTO(
            id: $user->getKey(),
            name: $user->name,
            email: $user->email,
            payload: ['deleted_at' => $user->getAttribute('deleted_at')->toDateTimeString()]
        );

        $this->dispatchLog($dto, LogActionEnum::DELETED, 'El usuario ha sido eliminado');
    }

    private function dispatchLog(UserJobDTO $dto, LogActionEnum $action, string $message): void
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
