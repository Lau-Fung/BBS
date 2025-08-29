<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_id','sim_id','vehicle_id','sensor_id',
        'is_installed','installed_on','removed_on','install_note','is_active',
    ];

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

    // Spatie Query Builder "scope filters"
    public function scopeExpiryFrom($q, $date) {
        $q->whereHas('sim', fn($s) => $s->whereDate('plan_expiry_at','>=',$date));
    }
    public function scopeExpiryTo($q, $date) {
        $q->whereHas('sim', fn($s) => $s->whereDate('plan_expiry_at','<=',$date));
    }
    public function scopeCapacityMin($q, $liters) {
        $q->whereHas('vehicle', fn($s) => $s->where('tank_capacity_liters','>=',(int)$liters));
    }
    public function scopeCapacityMax($q, $liters) {
        $q->whereHas('vehicle', fn($s) => $s->where('tank_capacity_liters','<=',(int)$liters));
    }
}
