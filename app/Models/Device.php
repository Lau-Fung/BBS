<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasAttachments;

class Device extends Model
{
    use SoftDeletes, HasAttachments;
    
    protected $fillable = ['imei','device_model_id','firmware','is_active'];
    protected $casts = ['is_active'=>'bool'];

    public function model() { 
        return $this->belongsTo(DeviceModel::class, 'device_model_id'); 
    }

    public function assignments() { 
        return $this->hasMany(Assignment::class); 
    }

    // FK: devices.device_model_id -> device_models.id
    public function deviceModel()
    {
        return $this->belongsTo(DeviceModel::class);
    }
}
