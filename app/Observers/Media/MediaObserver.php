<?php

namespace App\Observers\Media;

use App\Jobs\RecordActivityJob;
use App\Models\Media;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;

/**
 * Summary of MediaObserver
 * @author Francisco Rojas 
 * @abstract Observador para el modelo Media que registra actividades en logs.
 * @version 1.0
 * @since 2026-1-28 
 */
class MediaObserver
{
    /**
     * Summary of deleted
     * @abstract Escucha el evento "deleted" del modelo Media y registra la eliminación en los logs.
     * @param Media $media
     * @return void
     */
    public function deleted(Media $media): void
    {
        $mediaDto = new \App\DTOs\RecordActivityLogs\MediaJobDTO(
            id: $media->getKey(),
            path: $media->path,
            name: $media->name,
            mime_type: $media->mime_type,
            size: $media->size
        );

        RecordActivityJob::dispatch(
            $mediaDto,
            LogActionEnum::DELETED,
            LogLevelEnum::WARNING,
            'El media ha sido eliminado',
            request()->ip(),
            request()->userAgent(),
            request()->headers->get('referer'),
            auth()->user()
        );
    }
}
