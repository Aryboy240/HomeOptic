<?php

namespace App\Mail;

use App\Models\PendingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingDecisionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PendingBooking $booking,
        public readonly bool $approved,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->approved
            ? 'Your Appointment is Confirmed — HomeOptic'
            : 'Regarding Your Appointment Request — HomeOptic';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.booking-decision');
    }

    public function attachments(): array
    {
        return [];
    }
}
