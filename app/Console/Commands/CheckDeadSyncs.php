<?php

namespace App\Console\Commands;

use App\Enums\SyncStatusEnum;
use Illuminate\Console\Command;
use App\Models\StoreSyncState;
use App\Notifications\Alerts\StoreSyncAlertNotification;

class CheckDeadSyncs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:cleanup-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark syncs as failed if they have been running for more than 4 hours.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fourHoursAgo = now()->subHours(4);
        $staleSyncs = StoreSyncState::with('store')->where('sync_status', SyncStatusEnum::SYNCING->value)
            ->where('sync_started_at', '<', $fourHoursAgo)
            ->get();

        $staleSyncs->each(function ($sync) {
            $sync->sync_status = SyncStatusEnum::FAILED->value;
            $sync->sync_started_at = null;
            $sync->last_sync_error = 'Critical Error: The synchronization process for this store has exceeded the maximum allowed runtime of 4 hours and has been automatically marked as failed. Please investigate the underlying issues causing the delay and retry the sync operation.';
            $sync->last_error_at = now();

            StoreSyncAlertNotification::sendToAdmin($sync->store->name, $sync->last_sync_error);

            $sync->save();

            $this->info("Marked sync for store {$sync->store_id} ({$sync->store->name}) as failed due to timeout.");
        });

    }
}
