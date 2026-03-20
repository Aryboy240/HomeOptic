<?php

namespace App\Notifications\Strategies;

use App\Contracts\NotificationStrategy;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class SmsNotificationStrategy implements NotificationStrategy
{
    public function send(Appointment $appointment): void
    {
        // Stub — SMS gateway integration not yet implemented.
        // Would send an appointment reminder SMS to the patient's mobile number.
        Log::info('Appointment reminder SMS intent: would send to patient mobile.', [
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'mobile'         => $appointment->patient->telephone_mobile,
        ]);
    }
}
