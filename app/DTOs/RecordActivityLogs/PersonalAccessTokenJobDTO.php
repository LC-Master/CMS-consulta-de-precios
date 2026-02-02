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
        public string $store_id,
        public string $store_name,
        public string $store_code,
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
            'store_id' => $this->store_id,
            'store_name' => $this->store_name,
            'store_code' => $this->store_code,
        ];
    }
}
