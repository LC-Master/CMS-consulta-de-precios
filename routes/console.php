<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('campaigns:check')->everyTenMinutes();
Schedule::command('app:check-expiring-campaigns-eight')->hourly();
Schedule::command('app:check-expiring-campaigns')->daily();