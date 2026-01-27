<?php

namespace App\DTOs\RecordActivityLogs;

readonly class PersonalAccessTokenJobDTO
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
}
