<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum HowHeard: string
{
    use HasOptions;

    case Facebook     = 'facebook';
    case Google       = 'google';
    case InYourArea   = 'in_your_area';
    case Instagram    = 'instagram';
    case RecallLetter = 'recall_letter';
    case Recommended  = 'recommended';
    case WalkingPast  = 'walking_past';
    case Other        = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Facebook     => 'Facebook',
            self::Google       => 'Google',
            self::InYourArea   => 'In Your Area (Online Local News)',
            self::Instagram    => 'Instagram',
            self::RecallLetter => 'Recall Letter',
            self::Recommended  => 'Recommended',
            self::WalkingPast  => 'Walking Past',
            self::Other        => 'Other (Please Specify)',
        };
    }
}
