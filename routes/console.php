<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send day-before appointment reminders every morning at 08:00.
// Requires the scheduler to be running: `php artisan schedule:run` (cron: * * * * *)
Schedule::command('reminders:send-day-before')->dailyAt('08:00');
