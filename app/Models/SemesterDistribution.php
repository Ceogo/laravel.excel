<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterDistribution extends Model
{
    protected $fillable = ['learning_outcome_id', 'exams', 'credits', 'course_works', 'control_works'];

    public function learningOutcome()
    {
        return $this->belongsTo(LearningOutcome::class);
    }
}
