<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AppointmentStatus: string
{
    use HasOptions;

    case Booked        = 'booked';
    case Confirmed     = 'confirmed';
    case Completed     = 'completed';
    case Cancelled     = 'cancelled';
    case DidNotAttend  = 'did_not_attend';

    public function label(): string
    {
        return match ($this) {
            self::Booked       => 'Booked',
            self::Confirmed    => 'Confirmed',
            self::Completed    => 'Completed',
            self::Cancelled    => 'Cancelled',
            self::DidNotAttend => 'Did Not Attend',
        };
    }
}
