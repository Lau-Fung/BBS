<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sensor extends Model {
    
    use SoftDeletes;

    protected $fillable = ['serial_or_bt_id','sensor_model_id','notes'];

    public function model(){ 
        return $this->belongsTo(SensorModel::class,'sensor_model_id'); 
    }

}
