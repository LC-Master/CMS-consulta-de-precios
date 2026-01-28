<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ActivityLog;
use App\Models\User;

class RecordCampaignActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected ?User $causer,
        protected $subject,
        protected $action,
        protected $level,
        protected string $message,
        protected array $properties = []
    ) {}

    public function handle(): void
    {
        ActivityLog::create([
            'causer_id'      => $this->causer ? (string)$this->causer->id : 'system',
            'user_name'    => $this->causer?->name ?? 'System',
            'user_email'   => $this->causer?->email ?? 'system@cms.local',
            'subject_id'   => $this->subject?->id,
            'subject_type' => $this->subject ? get_class($this->subject) : null,
            'action'       => $this->action,
            'level'        => $this->level,
            'message'      => $this->message,
            'properties'   => $this->properties,
            'ip_address'   => request()->ip() ?? '127.0.0.1',
            'user_agent'   => request()->userAgent() ?? 'System',
        ]);
    }
}
