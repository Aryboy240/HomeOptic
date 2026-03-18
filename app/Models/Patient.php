<?php

namespace App\Models;

use App\Enums\DomiciliaryReason;
use App\Enums\DroppedReason;
use App\Enums\HowHeard;
use App\Enums\PatientStatus;
use App\Enums\PatientType;
use App\Enums\SexGender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date_of_birth'      => 'date',
        'status'             => PatientStatus::class,
        'sex_gender'         => SexGender::class,
        'patient_type'       => PatientType::class,
        'dropped_reason'     => DroppedReason::class,
        'how_heard'          => HowHeard::class,
        'domiciliary_reason' => DomiciliaryReason::class,
        'has_glaucoma'       => 'boolean',
        'is_diabetic'        => 'boolean',
        'is_nhs'             => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function pct(): BelongsTo
    {
        return $this->belongsTo(Pct::class);
    }

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Default search scope: only active patients.
     * Omit this scope when the "Include Deceased & Deleted" checkbox is ticked.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', PatientStatus::Active);
    }
}
