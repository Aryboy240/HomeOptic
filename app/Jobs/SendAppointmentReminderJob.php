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
 * Sends an appointment reminder notification via the appropriate channel.
 *
 * Strategy selection (via NotificationStrategyFactory):
 *   - EmailNotificationStrategy  — used when the patient has an email address.
 *   - SmsNotificationStrategy    — used when the patient has a mobile number but no email (stub).
 *   - LetterNotificationStrategy — fallback when neither contact method is available (stub).
 *
 * The factory encapsulates the selection logic so the job remains agnostic
 * of which channel is used. Swap or extend strategies without touching this class.
 */
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

        $strategy = NotificationStrategyFactory::for($appointment->patient);
        $strategy->send($appointment);

        // Record when the notification was last sent, used by the diary "Update & Notify" flow.
        $appointment->update(['notified_at' => now()]);
    }
}
