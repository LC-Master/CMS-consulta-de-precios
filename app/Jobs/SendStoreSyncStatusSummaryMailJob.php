<?php

namespace App\Jobs;

use App\Enums\SyncStatusEnum;
use App\Models\User;
use App\Notifications\StoreSyncNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class SendStoreSyncStatusSummaryMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $status)
    {
    }

    public function handle(): void
    {
        $statusEnum = SyncStatusEnum::tryFrom($this->status);

        if (! $statusEnum) {
            return;
        }

        $storesKey = "store_sync_summary_mail:{$this->status}:stores";
        $scheduledKey = "store_sync_summary_mail:{$this->status}:scheduled";

        $stores = Cache::pull($storesKey, []);
        Cache::forget($scheduledKey);

        if (! is_array($stores) || empty($stores)) {
            return;
        }

        $stores = array_values(array_unique(array_filter(array_map('strval', $stores))));

        if (empty($stores)) {
            return;
        }

        $users = User::role(['admin', 'supervisor'])->get();

        $notification = method_exists(StoreSyncNotification::class, 'summary')
            ? StoreSyncNotification::summary($statusEnum, $stores)
            : new StoreSyncNotification('Resumen', $statusEnum, $stores);

        Notification::send($users, $notification);
    }
}
