<?php

namespace App\Observers\Token;

use App\DTOs\RecordActivityLogs\PersonalAccessTokenJobDTO;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Jobs\RecordActivityJob;
use Laravel\Sanctum\PersonalAccessToken;

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
            LogActionEnum::TOKEN_CREATED,
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
            LogActionEnum::TOKEN_DELETED,
            LogLevelEnum::WARNING,
            'El token ha sido eliminado',
            request()->ip(),
            request()->userAgent(),
            request()->headers->get('referer'),
            auth()->user()
        );
    }
}
