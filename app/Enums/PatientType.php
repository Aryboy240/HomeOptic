<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PatientType: string
{
    use HasOptions;

    case Private       = 'A';
    case Over60        = 'B';
    case NHS           = 'C';
    case FamilyHistory = 'D';
    case Child         = 'E';
    case Glaucoma      = 'F';

    public function label(): string
    {
        return match ($this) {
            self::Private       => 'A — Private Patient',
            self::Over60        => 'B — Over 60',
            self::NHS           => 'C — NHS',
            self::FamilyHistory => 'D — Family History of Glaucoma',
            self::Child         => 'E — Child (Under 16)',
            self::Glaucoma      => 'F — Glaucoma',
        };
    }
}
