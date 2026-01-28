<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\User;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Contracts\LoggableDTO;

class RecordActivityJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public LoggableDTO $dto,
        public LogActionEnum $action,
        public LogLevelEnum $level,
        public ?string $message = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $referer = null,
        public User|null $causer = null,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ActivityLog::create([
            'subject_id' => (string) $this->dto->getId(),
            'subject_type' => $this->dto->getType(),
            'properties' => json_encode($this->dto->getProperties()),

            'action' => $this->action,
            'level' => $this->level,
            'message' => $this->message,

            'causer_id' => $this->causer?->getKey(),
            'user_name' => $this->causer?->name ?? 'Sistema',
            'user_email' => $this->causer?->email ?? 'N/A',

            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ]);
    }
}
