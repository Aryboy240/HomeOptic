<?php

namespace App\Jobs;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Appointment $appointment)
    {
    }

    public function handle(): void
    {
        // Re-load relations — SerializesModels restores the bare model without eager loads.
        $appointment = $this->appointment->load(['patient', 'diary']);

        if (empty($appointment->patient->email)) {
            Log::info('Appointment reminder skipped: patient has no email address on record.', [
                'appointment_id' => $appointment->id,
                'patient_id'     => $appointment->patient_id,
            ]);

            return;
        }

        Mail::to($appointment->patient->email)
            ->send(new AppointmentReminderMail($appointment));

        // Record when the notification was last sent, used by the diary "Update & Notify" flow.
        $appointment->update(['notified_at' => now()]);
    }
}
