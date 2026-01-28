<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use App\Models\Media;
/**
 * Summary of MediaJobDTO
 * @author Francisco Rojas
 * @abstract DTO para el modelo Media que se utiliza para registrar actividades en logs.
 * @version 1.0
 * @since 2026-1-28
 */
readonly class MediaJobDTO implements LoggableDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $id,
        public string $path,
        public string $name,
        public string $mime_type,
        public int $size
    ) {
        //
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getType(): string
    {
        return Media::class;
    }
    public function getProperties(): array
    {
        return [
            'path' => $this->path,
            'name' => $this->name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
        ];
    }

}
