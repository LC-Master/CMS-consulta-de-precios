<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;

class LowDiskSpace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:low-disk-space';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is intended to be run when the system detects low disk space. It can be used to perform actions such as sending notifications to administrators, cleaning up temporary files, or any other necessary measures to mitigate the low disk space issue.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stores = Store::with('syncState')->get();

        $stores = $stores->each(function ($store) {
            $syncState = $store->syncState;

            if ($syncState && isset($syncState->disk)) {
                $diskInfo = $syncState->disk;

                $size = isset($diskInfo['size']) ? (float) $diskInfo['size'] : null;
                $free = isset($diskInfo['free']) ? (float) $diskInfo['free'] : null;
                $used = isset($diskInfo['used']) ? (float) $diskInfo['used'] : null;

                if ($free === null && $size !== null && $used !== null) {
                    $free = $size - $used;
                }

                if ($size === null && isset($diskInfo['total'])) {
                    $size = (float) $diskInfo['total'];
                }

                if ($free !== null && $size !== null && $size > 0) {
                    $freePercentage = ($free / $size) * 100;

                    if ($freePercentage < 10) { 
                        \App\Notifications\Alerts\LowDiskSpaceNotification::sendToAdmins($store->name, (float) number_format($freePercentage, 2));
                        $this->warn("Store ID {$store->id} has low disk space: " . number_format($freePercentage, 2) . "% free.");
                    }
                }
            }
        });
    }
}
