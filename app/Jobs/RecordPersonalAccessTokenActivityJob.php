<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\Log\LogActionEnum;
use App\Enums\Log\LogLevelEnum;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class RecordPersonalAccessTokenActivityJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PersonalAccessToken $token,
        public LogActionEnum $action,
        public LogLevelEnum $level,
        public ?string $message = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $center = $this->token->tokenable;

        ActivityLog::create([
            'subject_id' => $this->token->getKey(),
            'subject_type' => PersonalAccessToken::class,
            'causer_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'user_email' => Auth::user()->email,
            'action' => $this->action,
            'level' => $this->level,
            'message' => $this->message,
            'properties' => json_encode([
                'token_name' => $this->token->getAttribute('name'),
                'center_id' => $center->id,
                'center_name' => $center->name,
                'center_code' => $center->code
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'referer' => request()->header('Referer'),
        ]);
    }
}
