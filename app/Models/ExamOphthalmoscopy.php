<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamOphthalmoscopy extends Model
{
    // Table name does not follow Laravel's default plural inflection (ophthalmoscopies)
    protected $table = 'exam_ophthalmoscopy';

    protected $guarded = ['id'];

    // No enum casts — all per-eye fields are string dropdown values
    // stored verbatim from the UI options.

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }
}
