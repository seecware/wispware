<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfrastructureDevice extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'serial_number',
        'mac_address',
        'status',
        'comment',
        'photo_path',
    ];

    public function deviceManagementInfo() {
        return $this->hasOne(deviceManagementInfo::class);
    }
}
