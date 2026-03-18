<?php

namespace App\Models;

use App\Enums\ExamOutcome;
use App\Enums\PatientType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRefraction extends Model
{
    // Table name does not follow Laravel's default plural inflection (exam_refractions)
    protected $table = 'exam_refraction';

    protected $guarded = ['id'];

    protected $casts = [
        'outcome'             => ExamOutcome::class,
        'retest_patient_type' => PatientType::class,
        // Recommendation checkboxes
        'rec_distance'        => 'boolean',
        'rec_near'            => 'boolean',
        'rec_intermediate'    => 'boolean',
        'rec_high_index'      => 'boolean',
        'rec_bifocals'        => 'boolean',
        'rec_varifocals'      => 'boolean',
        'rec_occupational'    => 'boolean',
        'rec_min_sub'         => 'boolean',
        'rec_photochromic'    => 'boolean',
        'rec_hardcoat'        => 'boolean',
        'rec_tint'            => 'boolean',
        'rec_mar'             => 'boolean',
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }
}
