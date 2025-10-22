<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WanStatTotalsReal extends Model
{

    protected $fillable = [
        'link_name',
        'link_type',
        'region',
        'is_wan_stat',
        'bandwidth_bits',
        'pool_name',       // or 'pool_id' if you use foreign key
        'traffic_in',
        'traffic_out',
        'q_95_in',
        'q_95_out',
        'start_datetime',
        'end_datetime',
    ];
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    public function metaData()
    {
        return $this->hasOne(WanMetaData::class);
    }

    public function pool()
    {
        return $this->hasOne(Pool::class, 'name', 'link_name');
    }
    // shortcut to device (through pool)
    public function device()
    {
        return $this->hasOneThrough(
            Device::class,   // final model
            Pool::class,     // intermediate model
            'name',          // FK on Pool (maps to WanStatTotal.link_name)
            'id',            // FK on Device
            'link_name',     // local key on WanStatTotal
            'device_id'      // local key on Pool
        );
    }
}
