<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YearTotal extends Model
{
    protected $fillable = ['module_id', 'total_hours'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
