<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PatientTitle: string
{
    use HasOptions;

    case Mr   = 'Mr';
    case Mrs  = 'Mrs';
    case Miss = 'Miss';
    case Ms   = 'Ms';
    case Dr   = 'Dr';
    case Prof = 'Prof';
    case Rev  = 'Rev';

    public function label(): string
    {
        return $this->value;
    }
}
