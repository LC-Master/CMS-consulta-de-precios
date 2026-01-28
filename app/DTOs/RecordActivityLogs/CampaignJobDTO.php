<?php

namespace App\DTOs\RecordActivityLogs;

use App\Contracts\LoggableDTO;
use App\Models\Campaign;

class CampaignJobDTO implements LoggableDTO
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
            'title' => $this->title,
            'payload' => $this->payload,
            'changes' => $this->changes,
        ];

    }

}
