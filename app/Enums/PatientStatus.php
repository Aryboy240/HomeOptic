<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PatientStatus: string
{
    use HasOptions;

    case Active   = 'active';
    case Deceased = 'deceased';
    case Deleted  = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::Deceased => 'Deceased',
            self::Deleted  => 'Deleted',
        };
    }
}
