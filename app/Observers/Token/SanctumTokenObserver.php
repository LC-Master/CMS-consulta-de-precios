<?php

namespace App\Observers\Token;

use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Jobs\RecordPersonalAccessTokenActivityJob;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenObserver
{
    public function created(PersonalAccessToken $token)
    {
        RecordPersonalAccessTokenActivityJob::dispatch($token, LogActionEnum::TOKEN_CREATED, LogLevelEnum::SUCCESS, 'Se ha creado un nuevo token de acceso personal.');
    }
    public function deleted(PersonalAccessToken $token)
    {
        RecordPersonalAccessTokenActivityJob::dispatch($token, LogActionEnum::TOKEN_DELETED, LogLevelEnum::WARNING, 'El token ha sido eliminado');
    }

}
