<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

// NOTE: The reference document lists only the first six values explicitly (marked with "...").
// The full list requires confirmation from the client before go-live.
enum DomiciliaryReason: string
{
    use HasOptions;

    case Agoraphobic  = 'agoraphobic';
    case Alzheimers   = 'alzheimers';
    case Amputee      = 'amputee';
    case Angina       = 'angina';
    case Arthritis    = 'arthritis';
    case BellsPalsy   = 'bells_palsy';

    public function label(): string
    {
        return match ($this) {
            self::Agoraphobic => 'Agoraphobic',
            self::Alzheimers  => "Alzheimer's",
            self::Amputee     => 'Amputee',
            self::Angina      => 'Angina',
            self::Arthritis   => 'Arthritis',
            self::BellsPalsy  => "Bell's Palsy",
        };
    }
}
