<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ActivityLog; 

class RecordCampaignActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $userId,
        protected $subject,
        protected $action,
        protected $level,
        protected string $message,
        protected array $properties = []
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'user_id'      => $this->userId ?: null,
            'subject_id'   => $this->subject->getKey(),
            'subject_type' => \get_class($this->subject),
            'action'       => $this->action,
            'level'        => $this->level,
            'message'      => $this->message,
            'properties'   => $this->properties,
            'ip_address'   => request()->ip() ?? '127.0.0.1',
            'user_agent'   => request()->userAgent() ?? 'System',
        ]);
    }
}