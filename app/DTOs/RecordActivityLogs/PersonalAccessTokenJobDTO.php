<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Summary of PersonalAccessTokenJobDTO
 * @author Francisco Rojas
 * @abstract DTO para el modelo PersonalAccessToken que se utiliza para registrar actividades en logs.
 * @version 1.0
 * @since 2026-1-28
 */
readonly class PersonalAccessTokenJobDTO implements LoggableDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $id,
        public string $token_name,
        public string $center_id,
        public string $center_name,
        public string $center_code,
    ) {
        //
    }
    public function getId(): string
    {
        return (string) $this->id;
    }
    public function getType(): string
    {
        return PersonalAccessToken::class;
    }
    public function getProperties(): array
    {
        return [
            'token_name' => $this->token_name,
            'center_id' => $this->center_id,
            'center_name' => $this->center_name,
            'center_code' => $this->center_code,
        ];
    }
}
