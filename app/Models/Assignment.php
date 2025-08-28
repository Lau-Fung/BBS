<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    public function device() { 
        return $this->belongsTo(Device::class); 
    }

    public function sim() { 
        return $this->belongsTo(Sim::class); 
    }

    public function vehicle() { 
        return $this->belongsTo(Vehicle::class); 
    }

    public function sensor() { 
        return $this->belongsTo(Sensor::class); 
    }
}
