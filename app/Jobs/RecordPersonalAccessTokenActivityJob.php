<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Models\ActivityLog;
use Laravel\Sanctum\PersonalAccessToken;
use App\DTOs\RecordActivityLogs\PersonalAccessTokenJobDTO;
class RecordPersonalAccessTokenActivityJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct(
        public PersonalAccessTokenJobDTO $token,
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

        $causer_id = $this->causer?->getKey();
        $user_name = $this->causer?->name ?? 'Sistema';
        $user_email = $this->causer?->email ?? 'N/A';

        ActivityLog::create([
            'subject_id' => (string) $this->token->id,
            'subject_type' => PersonalAccessToken::class,
            'causer_id' => $causer_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'action' => $this->action,
            'level' => $this->level,
            'message' => $this->message,
            'properties' => json_encode([
                'token_name' => $this->token->token_name,
                'center_id' => $this->token->center_id,
                'center_name' => $this->token->center_name,
                'center_code' => $this->token->center_code
            ]),
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'referer' => $this->referer,
        ]);
    }
}
