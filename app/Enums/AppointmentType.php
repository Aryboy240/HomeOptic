<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

// NOTE: The reference document does not specify appointment type values — these are
// placeholder values to be confirmed with the client before go-live.
enum AppointmentType: string
{
    use HasOptions;

    case RoutineEyeTest  = 'routine_eye_test';
    case Domiciliary     = 'domiciliary';
    case ContactLens     = 'contact_lens';
    case FollowUp        = 'follow_up';
    case Emergency       = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::RoutineEyeTest => 'Routine Eye Test',
            self::Domiciliary    => 'Domiciliary Visit',
            self::ContactLens    => 'Contact Lens Appointment',
            self::FollowUp       => 'Follow-Up',
            self::Emergency      => 'Emergency',
        };
    }
}
