<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumptionLog extends Model
{
    protected $fillable = [
        'queue_name',
        'consumption_gb',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'consumption_gb' => 'float',
    ];
}
