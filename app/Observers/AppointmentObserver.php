<?php

namespace App\Observers;

use App\Jobs\SendAppointmentReminderJob;
use App\Models\Appointment;

class AppointmentObserver
{
    /**
     * Dispatch a reminder job whenever a new appointment is booked.
     * The job handles the case where the patient has no email address on record.
     */
    public function created(Appointment $appointment): void
    {
        SendAppointmentReminderJob::dispatch($appointment);
    }
}
