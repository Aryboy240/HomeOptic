<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendingBooking extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'appointment_date'  => 'date',
        'admin_decision_at' => 'datetime',
        'patient_form_data' => 'array',
    ];

    public function adminDecidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_decided_by');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }
}
