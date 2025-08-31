<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'hostname',
    ];

    public function interfaces(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DeviceInterface::class);
    }
}
