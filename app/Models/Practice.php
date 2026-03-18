<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Practice extends Model
{
    protected $guarded = ['id'];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
