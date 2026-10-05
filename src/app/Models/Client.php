<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'payment_ammmount',
        'f_lastname',
        'm_lastname',
        'unified_sys_id',
        'assigned_speed_kb',
        'address',
        'phone',
        'photo_path',
        'notes',
        'signup_date',
    ];

    protected $casts = [
        'signup_date'=>'date',
    ];
}
