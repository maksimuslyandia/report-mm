<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceInterface extends Model
{
    protected $fillable = [
        'name',
        'device_id',
        'device_interface_id',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function parentInterface()
    {
        return $this->belongsTo(DeviceInterface::class, 'device_interface_id');
    }

    public function childInterfaces()
    {
        return $this->hasMany(DeviceInterface::class, 'device_interface_id');
    }
}
