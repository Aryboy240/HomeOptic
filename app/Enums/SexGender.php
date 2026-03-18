<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum SexGender: string
{
    use HasOptions;

    case Male           = 'male';
    case Female         = 'female';
    case NonBinary      = 'non_binary';
    case Transgender    = 'transgender';
    case PreferNotToSay = 'prefer_not_to_say';

    public function label(): string
    {
        return match ($this) {
            self::Male           => 'Male',
            self::Female         => 'Female',
            self::NonBinary      => 'Non-Binary',
            self::Transgender    => 'Transgender',
            self::PreferNotToSay => 'Prefer Not To Say',
        };
    }
}
