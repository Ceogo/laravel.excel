<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterDistribution extends Model
{
    protected $fillable = ['module_id', 'exams', 'credits', 'course_works', 'control_works'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
