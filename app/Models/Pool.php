<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    protected $fillable = [
        'name',
        'device_id',
        'interface_id',
    ];

    // Relationships
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function interface()
    {
        return $this->belongsTo(Interface::class);
    }
}
