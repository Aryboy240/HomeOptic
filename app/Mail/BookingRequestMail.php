<?php

namespace App\Mail;

use App\Models\PendingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly PendingBooking $booking)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Booking Request — HomeOptic');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.booking-request');
    }

    public function attachments(): array
    {
        return [];
    }
}
