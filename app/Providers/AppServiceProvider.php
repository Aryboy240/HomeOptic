<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Appointment;
use App\Models\Examination;
use App\Observers\AppointmentObserver;
use App\Observers\ExaminationObserver;
use Illuminate\Support\Facades\View;
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

        // Share unread notification count with the admin navigation
        View::composer('layouts.navigation', function ($view) {
            $unreadCount = auth()->check()
                ? AdminNotification::whereNull('read_at')->count()
                : 0;

            $view->with('unreadCount', $unreadCount);
        });
    }
}
