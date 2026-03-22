<?php

namespace App\Jobs;

use App\Mail\BookingReminderMail;
use App\Models\PendingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly PendingBooking $pendingBooking)
    {
    }

    public function handle(): void
    {
        Mail::to(config('mail.from.address'))->send(new BookingReminderMail($this->pendingBooking));
    }
}
