<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['plate','tank_capacity_liters','status','crm_no','notes','supervisor_user_id'];

    public function supervisor() { 
        return $this->belongsTo(User::class, 'supervisor_user_id'); 
    }

    public function scopeCapacityMin($q,$v){ 
        return $q->where('tank_capacity_liters','>=',(int)$v); 
    }

    public function scopeCapacityMax($q,$v){ 
        return $q->where('tank_capacity_liters','<=',(int)$v); 
    }
}
