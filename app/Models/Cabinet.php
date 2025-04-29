<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabinet extends Model
{
    protected $fillable = [
        'number', 'description', 'capacity',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'cabinet_id');
    }
}
