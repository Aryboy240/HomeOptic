<?php

namespace App\Models;

use App\Enums\ExamType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Examination extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'exam_type'   => ExamType::class,
        'examined_at' => 'date',
        'signed_at'   => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /** The optometrist who conducted the examination. */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /** The optometrist who signed off the examination. */
    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    // Tab child records — each is 1:1 and created by ExaminationFactory
    public function historySymptoms(): HasOne
    {
        return $this->hasOne(ExamHistorySymptom::class);
    }

    public function ophthalmoscopy(): HasOne
    {
        return $this->hasOne(ExamOphthalmoscopy::class);
    }

    public function investigative(): HasOne
    {
        return $this->hasOne(ExamInvestigative::class);
    }

    public function refraction(): HasOne
    {
        return $this->hasOne(ExamRefraction::class);
    }
}
