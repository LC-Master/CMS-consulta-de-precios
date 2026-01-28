<?php

namespace App\Observers\User;

use App\DTOs\RecordActivityLogs\UserJobDTO;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Jobs\RecordActivityJob;

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
            $changes = [
                'before' => array_intersect_key($user->getOriginal(), $user->getChanges()),
                'after' => $user->getChanges(),
            ];

            $dto = new UserJobDTO(
                id: $user->getKey(),
                name: $user->name,
                email: $user->email,
                payload: $user->getOriginal(),
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
