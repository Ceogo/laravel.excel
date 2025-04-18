<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicYearDetail extends Model
{
    protected $fillable = [
        'module_id',
        'total_hours',
        'theoretical_hours',
        'lab_practical_hours',
        'course_works',
        'professional_training'
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
