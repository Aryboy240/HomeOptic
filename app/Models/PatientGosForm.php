<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientGosForm extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_eligible'     => 'boolean',
        'admin_override'  => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Effective eligibility: admin override wins if set, otherwise use auto-calculated value.
     */
    public function effectiveEligibility(): bool
    {
        return $this->admin_override ?? $this->is_eligible;
    }
}
