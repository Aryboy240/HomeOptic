<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function pendingBooking(): BelongsTo
    {
        return $this->belongsTo(PendingBooking::class);
    }
}
