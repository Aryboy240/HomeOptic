<?php

namespace App\Models;

use App\Enums\IopMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamInvestigative extends Model
{
    // Table name does not follow Laravel's default plural inflection (exam_investigatives)
    protected $table = 'exam_investigative';

    protected $guarded = ['id'];

    protected $casts = [
        'drops_used'      => 'boolean',
        'drops_expiry'    => 'date',
        'pre_iop_method'  => IopMethod::class,
        'post_iop_method' => IopMethod::class,
    ];

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }
}
