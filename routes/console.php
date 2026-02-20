<?php

use App\Console\Commands\LowDiskSpace;
use Illuminate\Foundation\Inspiring;
use App\Console\Commands\CheckDeadSyncs;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckStoreSyncStatus;
use Carbon\Carbon;

$margin = 30;

$morning = Carbon::createFromFormat('H:i', config('services.syncMorningStart'))->addMinutes($margin)->format('H:i');
$afternoon = Carbon::createFromFormat('H:i', config('services.syncAfternoonStart'))->addMinutes($margin)->format('H:i');

Schedule::command(CheckDeadSyncs::class)->everyTenMinutes();
Schedule::command(CheckStoreSyncStatus::class)->dailyAt($morning);
Schedule::command(CheckStoreSyncStatus::class)->dailyAt($afternoon);
Schedule::command(LowDiskSpace::class)->dailyAt('7:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:soft-delete-old-logs')->monthly();
Schedule::command('app:force-delete-old-logs')->monthlyOn(1, '2:00');

Schedule::command('campaigns:check')->everyTenMinutes();
Schedule::command('app:check-expiring-campaigns-eight')->hourly();
Schedule::command('app:check-expiring-campaigns')->daily();
Schedule::command('media:clean-old')->monthlyOn(1, '00:00');