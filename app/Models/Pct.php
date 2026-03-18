<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pct extends Model
{
    protected $guarded = ['id'];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
