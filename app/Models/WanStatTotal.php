<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WanStatTotal extends Model
{
    protected $fillable = [
        'link_name',
        'link_type',
        'region',
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

}
