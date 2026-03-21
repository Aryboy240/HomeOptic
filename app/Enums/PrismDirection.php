<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PrismDirection: string
{
    use HasOptions;

    case In      = 'in';
    case Out     = 'out';
    case Up      = 'up';
    case Down    = 'down';
    case UpIn    = 'up_in';
    case UpOut   = 'up_out';
    case DownIn  = 'down_in';
    case DownOut = 'down_out';

    public function label(): string
    {
        return match ($this) {
            self::In      => 'In',
            self::Out     => 'Out',
            self::Up      => 'Up',
            self::Down    => 'Down',
            self::UpIn    => 'Up/In',
            self::UpOut   => 'Up/Out',
            self::DownIn  => 'Down/In',
            self::DownOut => 'Down/Out',
        };
    }
}
