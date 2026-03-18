<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date'               => 'date',
        'appointment_type'   => AppointmentType::class,
        'appointment_status' => AppointmentStatus::class,
        'cancelled_at'       => 'datetime',
        'notified_at'        => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function diary(): BelongsTo
    {
        return $this->belongsTo(Diary::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Exclude cancelled appointments — the default diary view.
     * The "show cancelled" toggle omits this scope.
     */
    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->whereNull('cancelled_at');
    }

    /**
     * Filter to appointments whose date falls within an inclusive date range.
     * Used by the diary week/day views.
     */
    public function scopeForDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('date', [$from->toDateString(), $to->toDateString()]);
    }
}
