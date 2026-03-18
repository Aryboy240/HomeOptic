<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ExamOutcome: string
{
    use HasOptions;

    case NoChangeSpecsNeeded = 'no_change_specs_needed';
    case NoChangeSpecsOk     = 'no_change_specs_ok';
    case NewRx               = 'new_rx';
    case NoRx                = 'no_rx';
    case ScreeningOnly       = 'screening_only';
    case ReferToGp           = 'refer_to_gp';

    public function label(): string
    {
        return match ($this) {
            self::NoChangeSpecsNeeded => 'No Change — Specs Needed',
            self::NoChangeSpecsOk     => 'No Change — Specs OK',
            self::NewRx               => 'New Rx',
            self::NoRx                => 'No Rx',
            self::ScreeningOnly       => 'Screening Only',
            self::ReferToGp           => 'Refer to GP',
        };
    }
}
