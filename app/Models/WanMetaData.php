<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WanMetaData extends Model
{
    protected $table = 'wan_meta_data';

    protected $fillable = [
        'airport_code', 'isp_type', 'wan_stat_total_id','is_ibo','isp'
    ];

    public function wanStatTotal()
    {
        return $this->belongsTo(WanStatTotal::class);
    }
}
