<?php

namespace App\Enums\Log;

enum LogActionEnum: string
{
    case CREATED = 'creado';
    case UPDATED = 'actualizado';
    case DELETED = 'eliminado';
    case RESTORED = 'restaurado';
    case PUBLISHED = 'publicado';
    case TOKEN_REFRESHED = 'token_actualizado';
    case TOKEN_CREATED = 'token_creado';
    case TOKEN_DELETED = 'token_eliminado';
}