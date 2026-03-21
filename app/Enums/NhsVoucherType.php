<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum NhsVoucherType: string
{
    use HasOptions;

    // Single Vision (Distance / Near)
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';

    // Bifocal / Varifocal
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case H = 'H';

    public function label(): string
    {
        return match ($this) {
            self::A => 'A — £42.40 (Sphere 0.25–2.00, Cyl Plano–6.00)',
            self::B => 'B — £64.26 (Sphere 6.25–9.75)',
            self::C => 'C — £94.14 (Sphere 10.00–14.00)',
            self::D => 'D — £212.40 (Sphere Over 14.00 or Cyl Over 6)',
            self::E => 'E — £73.10 (Sphere Plano–6.00)',
            self::F => 'F — £92.72 (Sphere 6.25–9.75)',
            self::G => 'G — £120.48 (Sphere 10.00–14.00)',
            self::H => 'H — £233.56 (Sphere Over 14.00 or Cyl Over 6)',
        };
    }
}
