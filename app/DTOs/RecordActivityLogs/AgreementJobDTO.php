<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use App\Models\Agreement;

/**
 * Summary of AgreementJobDTO
 * @author Francisco Rojas
 * @abstract DTO para el modelo Agreement que se utiliza para registrar actividades en logs.
 * @version 1.0
 * @since 2026-1-28
 */
readonly class AgreementJobDTO implements LoggableDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $id,
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
        return Agreement::class;
    }
    public function getProperties(): array
    {
        return [
            'id' => $this->id,
            'payload' => $this->payload,
            'changes' => $this->changes,
        ];
    }
}
