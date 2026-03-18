<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PrismDirection: string
{
    use HasOptions;

    case In   = 'in';
    case Out  = 'out';
    case Up   = 'up';
    case Down = 'down';

    public function label(): string
    {
        return match ($this) {
            self::In   => 'In',
            self::Out  => 'Out',
            self::Up   => 'Up',
            self::Down => 'Down',
        };
    }
}
