<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceManagementInfo extends Model
{
    protected $fillable = [
        'dev_id',
        'ip_management_address',
        'default_management_page',
        'default_username',
        'default_password',
        'default_ssid',
        'default_ssid_pw',
        'notes',
    ];

    public function infrastructureDevice() {
        return $this->belongsTo(InfrastructureDevice::class, 'dev_id');
    }
}
