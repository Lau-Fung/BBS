<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasAttachments;

class Assignment extends Model
{
    use SoftDeletes, HasAttachments;

    protected $fillable = [
        'device_id','sim_id','vehicle_id','sensor_id',
        'is_installed','installed_on','removed_on','install_note','is_active',
    ];

    protected $casts = [
        'is_installed' => 'boolean',
        'is_active'    => 'boolean',
        'installed_on' => 'date',
        'removed_on'   => 'date',
        'extras'   => 'array',
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

    public function scopeActive($q) 
    { 
        return $q->where('is_active', true); 
    }

    public function sheetRow()
    {
        return $this->hasOne(\App\Models\ClientSheetRow::class);
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
