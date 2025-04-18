<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = ['index', 'discipline_name', 'teacher_name'];

    public function semesterDistribution(): HasOne
    {
        return $this->hasOne(SemesterDistribution::class);
    }

    public function rupDetail(): HasOne
    {
        return $this->hasOne(RupDetail::class);
    }

    public function academicYearDetail(): HasOne
    {
        return $this->hasOne(AcademicYearDetail::class);
    }

    public function semesterDetails(): HasMany
    {
        return $this->hasMany(SemesterDetail::class);
    }

    public function yearTotal(): HasOne
    {
        return $this->hasOne(YearTotal::class);
    }
}
