<?php

namespace App\Mail;

use App\Models\PendingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly PendingBooking $booking)
    {
    }

    public function envelope(): Envelope
    {
        $name = ($this->booking->patient_form_data['first_name'] ?? '')
              . ' '
              . ($this->booking->patient_form_data['surname'] ?? '');

        return new Envelope(
            subject: 'Action Required — Unreviewed Booking Request (24h): ' . trim($name),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.booking-reminder',
            with: ['booking' => $this->booking],
        );
    }
}
