<?php

namespace App\Enums\Concerns;

trait HasOptions
{
    /**
     * Returns an associative array of [value => label] suitable for Blade select inputs.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->all();
    }
}
