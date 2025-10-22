<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WanMetaDataReal extends Model
{
    protected $table = 'wan_meta_data_reals';

    protected $fillable = [
        'airport_code', 'isp_type', 'wan_stat_totals_real_id','is_ibo','isp'
    ];

    public function wanStatTotal()
    {
        return $this->belongsTo(WanStatTotal::class);
    }
}
