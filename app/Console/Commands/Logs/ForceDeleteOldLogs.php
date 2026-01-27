<?php

namespace App\Console\Commands\Logs;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class ForceDeleteOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:force-delete-old-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminación permanente de los logs softdelete con una duración especifica';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subMonths(config('logs.force_delete_old_logs', 6));

        $logs = ActivityLog::onlyTrashed()->where('deleted_at', '<', $date);

        $count = $logs->count();

        $this->info("Se van a eliminar permanentemente {$count} logs antiguos softdelete a {$date->toDateString()}.");

        $logs->forceDelete();
    }
}
