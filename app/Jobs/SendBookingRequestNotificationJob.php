<?php

namespace App\Jobs;

use App\Mail\BookingRequestMail;
use App\Models\PendingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingRequestNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly PendingBooking $pendingBooking)
    {
    }

    public function handle(): void
    {
        Mail::to(config('mail.from.address'))->send(new BookingRequestMail($this->pendingBooking));
    }
}
