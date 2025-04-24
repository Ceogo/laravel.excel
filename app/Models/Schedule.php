<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'group_id',
        'learning_outcome_id',
        'day',
        'pair_number',
        'type',
        'week',
        'semester',
    ];
    public function learningOutcome()
    {
        return $this->belongsTo(LearningOutcome::class);
    }
}
