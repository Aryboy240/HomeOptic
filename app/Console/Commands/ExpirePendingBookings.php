<?php

namespace App\Console\Commands;

use App\Jobs\SendBookingDecisionJob;
use App\Jobs\SendBookingReminderJob;
use App\Models\AdminNotification;
use App\Models\PendingBooking;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature   = 'bookings:expire-pending {--dry-run : Log what would happen without making any changes}';
    protected $description = 'Send 24h reminder for unreviewed bookings and auto-decline at 48h';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // ── Step 1: Auto-decline bookings older than 48 hours ────────────────
        $toExpire = PendingBooking::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        if (!$dryRun) {
            foreach ($toExpire as $booking) {
                $booking->update([
                    'status'            => 'declined',
                    'admin_decision_at' => now(),
                    'admin_decided_by'  => null,
                ]);

                SendBookingDecisionJob::dispatch($booking, false);

                $name = trim(
                    ($booking->patient_form_data['first_name'] ?? '')
                    . ' '
                    . ($booking->patient_form_data['surname'] ?? '')
                );
                $date = $booking->appointment_date->format('j M Y');
                $time = substr($booking->appointment_time, 0, 5);

                AdminNotification::create([
                    'type'               => 'booking_auto_expired',
                    'title'              => 'Booking Auto-Expired',
                    'body'               => "{$name} — {$date} at {$time} was automatically declined after 48 hours without a response",
                    'pending_booking_id' => $booking->id,
                ]);
            }
        }

        $expireCount = $toExpire->count();
        $this->info("Auto-declined {$expireCount} booking(s) older than 48 hours.");

        // ── Step 2: Send 24h reminder for bookings between 24–48 hours old ──
        $toRemind = PendingBooking::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->where('created_at', '>', now()->subHours(48))
            ->whereNull('reminder_sent_at')
            ->get();

        if (!$dryRun) {
            foreach ($toRemind as $booking) {
                SendBookingReminderJob::dispatch($booking);
                $booking->update(['reminder_sent_at' => now()]);
            }
        }

        $remindCount = $toRemind->count();
        $this->info("Dispatched {$remindCount} 24h reminder(s) for unreviewed bookings.");

        if ($dryRun) {
            $this->warn("DRY RUN: Would decline {$expireCount} bookings, would send {$remindCount} reminders.");
        }

        return self::SUCCESS;
    }
}
