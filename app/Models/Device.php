<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public function model() { 
        return $this->belongsTo(DeviceModel::class, 'device_model_id'); 
    }

    public function assignments() { 
        return $this->hasMany(Assignment::class); 
    }
}
