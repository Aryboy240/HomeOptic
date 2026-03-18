<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

// NOTE: The reference document marks this list with "..." — the full set of GOS eligibility
// categories requires confirmation from the client before go-live.
enum GosEligibility: string
{
    use HasOptions;

    case NotEligible    = 'not_eligible';
    case ComplexRx      = 'complex_rx';
    case Diabetes       = 'diabetes';
    case FhgAnd40Over   = 'fhg_and_40_over';
    case Glaucoma       = 'glaucoma';
    case GlaucomaRisk   = 'glaucoma_risk';
    case Over60         = 'over_60';

    public function label(): string
    {
        return match ($this) {
            self::NotEligible  => 'Not Eligible',
            self::ComplexRx    => 'Complex Rx',
            self::Diabetes     => 'Diabetes',
            self::FhgAnd40Over => 'Family History of Glaucoma and 40 or Over',
            self::Glaucoma     => 'Glaucoma',
            self::GlaucomaRisk => 'Glaucoma Risk',
            self::Over60       => 'Over 60',
        };
    }
}
