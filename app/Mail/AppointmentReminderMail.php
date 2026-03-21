<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The appointment model is a public property so it is automatically
     * available as $appointment in the mail view without explicit with().
     */
    public function __construct(
        public readonly Appointment $appointment,
        public readonly string $reminderType = 'upcoming',
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->reminderType === 'tomorrow'
            ? 'Your Appointment is Tomorrow — HomeOptic'
            : 'Your Appointment Reminder — HomeOptic';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.appointment-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
