<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use App\Models\User;


/**
 * Summary of UserJobDTO
 * @author Francisco Rojas
 * @abstract Objecto de transferencia para el job que se encarga de guardar los logs
 * @since 2026-01-28
 */
readonly class UserJobDTO implements LoggableDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public array $payload = [],
        public array $changes = [],
    ) {
        //
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getType(): string
    {
        return User::class;
    }
    public function getProperties(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'payload' => $this->payload,
            'changes' => $this->changes,
        ];
    }
}
