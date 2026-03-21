<?php

namespace App\Notifications\Strategies;

use App\Contracts\NotificationStrategy;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class LetterNotificationStrategy implements NotificationStrategy
{
    public function send(Appointment $appointment): void
    {
        // Stub — letter generation not yet implemented.
        // Would generate and queue a physical appointment reminder letter to the patient's address.
        Log::info('Appointment reminder letter intent: would post letter to patient address.', [
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'address'        => implode(', ', array_filter([
                $appointment->patient->address_line_1,
                $appointment->patient->town_city,
                $appointment->patient->county,
                $appointment->patient->post_code,
            ])),
        ]);
    }
}
