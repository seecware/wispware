<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MikrotikSetting extends Model
{
    protected $fillable = 
    [
        'host',
        'port',
        'user',
    ];
}
