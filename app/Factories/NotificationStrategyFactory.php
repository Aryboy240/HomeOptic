<?php

namespace App\Factories;

use App\Contracts\NotificationStrategy;
use App\Models\Patient;
use App\Notifications\Strategies\EmailNotificationStrategy;
use App\Notifications\Strategies\LetterNotificationStrategy;
use App\Notifications\Strategies\SmsNotificationStrategy;

class NotificationStrategyFactory
{
    /**
     * Return the appropriate notification strategy for the given patient.
     *
     * Priority:
     *   1. Email  — patient has an email address.
     *   2. SMS    — patient has a mobile number but no email.
     *   3. Letter — fallback when neither email nor mobile is available.
     */
    public static function for(Patient $patient): NotificationStrategy
    {
        if (!empty($patient->email)) {
            return new EmailNotificationStrategy();
        }

        if (!empty($patient->telephone_mobile)) {
            return new SmsNotificationStrategy();
        }

        return new LetterNotificationStrategy();
    }
}
