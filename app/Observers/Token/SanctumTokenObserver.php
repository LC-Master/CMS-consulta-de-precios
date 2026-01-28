<?php

namespace App\Observers\Token;

use App\DTOs\RecordActivityLogs\PersonalAccessTokenJobDTO;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Jobs\RecordActivityJob;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @author Francisco Rojas
 * @abstract Observador para el modelo PersonalAccessToken que registra actividades en logs.
 * @version 1.0
 * @since 2026-1-28
 */
class SanctumTokenObserver
{
    public function created(PersonalAccessToken $token)
    {

        $personalAccessTokenJobDTO = new PersonalAccessTokenJobDTO(
            id: $token->getKey(),
            token_name: $token->getAttribute('name'),
            center_id: $token->tokenable?->id,
            center_name: $token->tokenable?->name,
            center_code: $token->tokenable?->code,
        );

        RecordActivityJob::dispatch(
            $personalAccessTokenJobDTO,
            LogActionEnum::CREATED,
            LogLevelEnum::SUCCESS,
            'Se ha creado un nuevo token de acceso personal.',
            request()->ip(),
            request()->userAgent(),
            request()->headers->get('referer'),
            auth()->user()
        );
    }

    public function deleted(PersonalAccessToken $token)
    {
        $personalAccessTokenJobDTO = new PersonalAccessTokenJobDTO(
            id: $token->getKey(),
            token_name: $token->getAttribute('name'),
            center_id: $token->tokenable?->id,
            center_name: $token->tokenable?->name,
            center_code: $token->tokenable?->code,
        );
        RecordActivityJob::dispatch(
            $personalAccessTokenJobDTO,
            LogActionEnum::DELETED,
            LogLevelEnum::WARNING,
            'El token ha sido eliminado',
            request()->ip(),
            request()->userAgent(),
            request()->headers->get('referer'),
            auth()->user()
        );
    }
}
