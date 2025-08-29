<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['imei','device_model_id','firmware','is_active'];
    protected $casts = ['is_active'=>'bool'];

    public function model() { 
        return $this->belongsTo(DeviceModel::class, 'device_model_id'); 
    }

    public function assignments() { 
        return $this->hasMany(Assignment::class); 
    }
}
