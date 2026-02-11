<?php

namespace App\Console\Commands;

use App\Enums\SyncStatusEnum;
use App\Models\Store;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Notifications\Alerts\ConsultorOfflineNotification;

class CheckStoreSyncStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stores:check-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks the synchronization status of the store. It verifies if the local store has not synced according to the hourly schedule, and if the store is not synced, it will send a notification to the admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $firstTime = config('services.syncMorningStart');
        $secondTime = config('services.syncAfternoonStart');

        $stores = Store::with('syncState')->get();

        $stores->each(function ($store) use ($now, $firstTime, $secondTime) {
            $syncState = $store->syncState;
            if (!$syncState) {
                $this->warn("Store ID {$store->id} has no sync state.");
                return;
            }

            // Parse configured times (expected format like "H:i") into today timestamps
            $firstTimeToday = null;
            $secondTimeToday = null;
            try {
                if ($firstTime) {
                    $firstTimeToday = Carbon::createFromFormat('H:i', $firstTime)->setDate($now->year, $now->month, $now->day);
                }
                if ($secondTime) {
                    $secondTimeToday = Carbon::createFromFormat('H:i', $secondTime)->setDate($now->year, $now->month, $now->day);
                }
            } catch (\Exception $e) {
                // If config values are not parseable, skip checks for safety
                $this->warn("Store ID {$store->id}: invalid sync time configuration.");
                return;
            }

            // Determine last sync time from syncState (try common attribute names)
            $lastSyncRaw = $syncState->last_synced_at ?? $syncState->last_reported_at ?? $syncState->updated_at ?? null;
            $lastSync = $lastSyncRaw ? Carbon::parse($lastSyncRaw) : null;

            // If already passed second sync time, require last sync after second time
            if ($secondTimeToday && $now->greaterThan($secondTimeToday)) {
                if (!$lastSync || $lastSync->lessThanOrEqualTo($secondTimeToday)) {
                    $this->error("Store ID {$store->id} has not synced after second scheduled time ({$secondTime}).");

                    // Enviar notificación indicando la hora que falló y el nombre de la tienda
                    $failedTime = $secondTime;
                    ConsultorOfflineNotification::sendToAdmins($store->name ?? $store->id, $failedTime);

                    // Marcar el estado de sync como fallido si es posible
                    try {
                        $syncState->sync_status = SyncStatusEnum::FAILED->value;
                        $syncState->save();
                    } catch (\Exception $e) {
                        $this->warn("Store ID {$store->id}: could not update syncState status: {$e->getMessage()}");
                    }
                } else {
                    $this->info("Store ID {$store->id} synced after second scheduled time.");
                }
                return;
            }

            // If already passed first sync time, require last sync after first time
            if ($firstTimeToday && $now->greaterThan($firstTimeToday)) {
                if (!$lastSync || $lastSync->lessThanOrEqualTo($firstTimeToday)) {
                    $this->error("Store ID {$store->id} has not synced after first scheduled time ({$firstTime}).");

                    // Enviar notificación indicando la hora que falló y el nombre de la tienda
                    $failedTime = $firstTime;
                    ConsultorOfflineNotification::sendToAdmins($store->name ?? $store->id, $failedTime);

                    // Marcar el estado de sync como fallido si es posible
                    try {
                        $syncState->sync_status = SyncStatusEnum::FAILED->value;
                        $syncState->save();
                    } catch (\Exception $e) {
                        $this->warn("Store ID {$store->id}: could not update syncState status: {$e->getMessage()}");
                    }
                } else {
                    $this->info("Store ID {$store->id} synced after first scheduled time.");
                }
                return;
            }

            $this->info("Store ID {$store->id}: sync window not yet reached.");
        });

    }
}
