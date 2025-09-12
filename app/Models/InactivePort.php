<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InactivePort extends Model
{
    protected $fillable = ['device_name', 'port'];
}
