<?php

namespace App\Jobs;

use App\Factories\NotificationStrategyFactory;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Sends a day-before appointment reminder notification.
 *
 * Uses the same NotificationStrategyFactory channel selection as
 * SendAppointmentReminderJob but passes reminderType='tomorrow' so the
 * email subject and body copy reflect the imminent appointment.
 */
class SendAppointmentDayBeforeReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Appointment $appointment)
    {
    }

    public function handle(): void
    {
        $appointment = $this->appointment->load(['patient', 'diary']);

        $strategy = NotificationStrategyFactory::for($appointment->patient, 'tomorrow');
        $strategy->send($appointment);

        $appointment->update(['day_before_notified_at' => now()]);
    }
}
