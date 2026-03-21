<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum IopMethod: string
{
    use HasOptions;

    case GAT     = 'gat';
    case ICare   = 'icare';
    case NCT     = 'nct';
    case Perkins = 'perkins';
    case TonoPen = 'tono_pen';

    public function label(): string
    {
        return match ($this) {
            self::GAT     => 'Goldmann Applanation Tonometry (GAT)',
            self::ICare   => 'iCare (Rebound Tonometry)',
            self::NCT     => 'Non-Contact Tonometry (NCT/Air-puff)',
            self::Perkins => 'Perkins Tonometer',
            self::TonoPen => 'Tono-Pen',
        };
    }
}
