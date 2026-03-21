<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum GosSubmissionStatus: string
{
    use HasOptions;

    case Unsubmitted          = 'unsubmitted';
    case AwaitingConfirmation = 'awaiting_confirmation';
    case Accepted             = 'accepted';
    case Rejected             = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Unsubmitted          => 'Unsubmitted',
            self::AwaitingConfirmation => 'Awaiting Confirmation',
            self::Accepted             => 'Accepted',
            self::Rejected             => 'Rejected',
        };
    }
}
