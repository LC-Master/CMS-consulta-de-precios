<?php

namespace App\Console\Commands\Logs;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class SoftDeleteOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:soft-delete-old-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminación con softdelete de los logs con una duración especifica';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subMonths(config('logs.soft_delete_old_logs', 3));

        $logs = ActivityLog::where('created_at', '<', $date);

        $count = $logs->count();

        $this->info("Se van a softdelete {$count} logs antiguos a {$date->toDateString()}.");

        $logs->delete();
    }
}
