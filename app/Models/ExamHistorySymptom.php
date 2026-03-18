<?php

namespace App\Models;

use App\Enums\GosEligibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamHistorySymptom extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'gos_eligibility'     => GosEligibility::class,
        'last_exam_first'     => 'boolean',
        'last_exam_not_known' => 'boolean',
        'last_exam_date'      => 'date',
        // JSON array of medication name strings. See FINAL_SCHEMA.md — normalise before production.
        'medications'         => 'array',
        'has_glaucoma'        => 'boolean',
        'has_fhg'             => 'boolean',
        'is_diabetic'         => 'boolean',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }
}
