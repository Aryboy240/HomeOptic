<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Examination;
use App\Observers\AppointmentObserver;
use App\Observers\ExaminationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Appointment::observe(AppointmentObserver::class);
        Examination::observe(ExaminationObserver::class);
    }
}
