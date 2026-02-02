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
    /**
     * Summary of created
     * @abstract Escucha el evento "created" del modelo PersonalAccessToken y registra la creación en los logs.
     * @param PersonalAccessToken $token
     * @return void
     */
    public function created(PersonalAccessToken $token)
    {

        $personalAccessTokenJobDTO = new PersonalAccessTokenJobDTO(
            id: $token->getKey(),
            token_name: $token->getAttribute('name'),
            store_id: $token->tokenable?->id,
            store_name: $token->tokenable?->name,
            store_code: $token->tokenable?->store_code,
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
    /**
     * Summary of deleted
     * @abstract Escucha el evento "deleted" del modelo PersonalAccessToken y registra la eliminación en los logs.
     * @param PersonalAccessToken $token
     * @return void
     */
    public function deleted(PersonalAccessToken $token)
    {
        $personalAccessTokenJobDTO = new PersonalAccessTokenJobDTO(
            id: $token->getKey(),
            token_name: $token->getAttribute('name'),
            store_id: $token->tokenable?->id,
            store_name: $token->tokenable?->name,
            store_code: $token->tokenable?->store_code,
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
