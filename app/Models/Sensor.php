<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasAttachments;

class Sensor extends Model {
    
    use SoftDeletes, HasAttachments;

    protected $fillable = ['serial_or_bt_id','sensor_model_id','notes'];

    public function model(){ 
        return $this->belongsTo(SensorModel::class,'sensor_model_id'); 
    }

    public function sensorModel()
    {
        return $this->belongsTo(SensorModel::class, 'sensor_model_id');
    }

}
