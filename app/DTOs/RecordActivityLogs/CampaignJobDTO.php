<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use App\Models\Campaign;

/**
 * Summary of CampaignJobDTO
 * @author Francisco Rojas
 * @abstract DTO para el modelo Campaign que se utiliza para registrar actividades en logs.
 * @version 1.0
 * @since 2026-1-28
 */
readonly class CampaignJobDTO implements LoggableDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $id,
        public string $title,
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
        return Campaign::class;
    }
    public function getProperties(): array
    {
        return [
            'payload' => $this->payload,
            'changes' => $this->changes,
        ];

    }

}
