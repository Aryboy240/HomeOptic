<?php

namespace App\Models;

use App\Enums\GosSubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GosSubmission extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status'       => GosSubmissionStatus::class,
        'submitted_at' => 'datetime',
        'paid_at'      => 'datetime',
        'form_data'    => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
