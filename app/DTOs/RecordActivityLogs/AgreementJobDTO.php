<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use App\Models\Agreement;

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
