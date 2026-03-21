<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentDayBeforeReminderJob;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDayBeforeReminders extends Command
{
    protected $signature   = 'reminders:send-day-before {--date= : Target date (Y-m-d) — defaults to tomorrow. For testing only.}';
    protected $description = 'Dispatch day-before reminder notifications for tomorrow\'s appointments';

    public function handle(): int
    {
        $tomorrow = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::tomorrow()->toDateString();

        $appointments = Appointment::query()
            ->whereDate('date', $tomorrow)
            ->whereNull('cancelled_at')
            ->where(function ($q) {
                $q->whereNull('day_before_notified_at')
                  ->orWhere('day_before_notified_at', '<', now()->subHours(20));
            })
            ->with(['patient', 'diary'])
            ->get();

        foreach ($appointments as $appointment) {
            SendAppointmentDayBeforeReminderJob::dispatch($appointment);
        }

        $count = $appointments->count();
        $this->info("Dispatched {$count} day-before reminder(s) for {$tomorrow}.");

        return self::SUCCESS;
    }
}
