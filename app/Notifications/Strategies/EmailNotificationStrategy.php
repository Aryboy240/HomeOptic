<?php

namespace App\Notifications\Strategies;

use App\Contracts\NotificationStrategy;
use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

class EmailNotificationStrategy implements NotificationStrategy
{
    public function __construct(private readonly string $reminderType = 'upcoming')
    {
    }

    public function send(Appointment $appointment): void
    {
        Mail::to($appointment->patient->email)
            ->send(new AppointmentReminderMail($appointment, $this->reminderType));
    }
}
