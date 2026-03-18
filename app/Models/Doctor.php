<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $guarded = ['id'];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
