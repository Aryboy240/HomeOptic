<?php

namespace App\Jobs;

use App\Mail\BookingDecisionMail;
use App\Models\PendingBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingDecisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly PendingBooking $pendingBooking,
        public readonly bool $approved,
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->pendingBooking->customer_email)
            ->send(new BookingDecisionMail($this->pendingBooking, $this->approved));
    }
}
