<?php

namespace App\Contracts;

use App\Models\Appointment;

interface NotificationStrategy
{
    /**
     * Send a notification for the given appointment using this strategy's channel.
     */
    public function send(Appointment $appointment): void;
}
