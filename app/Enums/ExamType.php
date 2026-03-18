<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ExamType: string
{
    use HasOptions;

    case Spectacle             = 'spectacle';
    case RedEye                = 'red_eye';
    case PostOp                = 'post_op';
    case ContactLens           = 'contact_lens';
    case ContactLensAftercare  = 'contact_lens_aftercare';
    case ExternalRx            = 'external_rx';
    case ExternalClRx          = 'external_cl_rx';

    public function label(): string
    {
        return match ($this) {
            self::Spectacle            => 'Spectacle Exam',
            self::RedEye               => 'Red Eye Exam',
            self::PostOp               => 'Post-Op',
            self::ContactLens          => 'Contact Lens Exam',
            self::ContactLensAftercare => 'Contact Lens Aftercare',
            self::ExternalRx           => 'External Rx',
            self::ExternalClRx         => 'External CL Rx',
        };
    }

    /**
     * Whether this exam type is fully implemented (prototype scope).
     */
    public function isSupported(): bool
    {
        return $this === self::Spectacle;
    }
}
