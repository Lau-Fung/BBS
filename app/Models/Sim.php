<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sim extends Model
{
    use SoftDeletes;

    protected $fillable = ['carrier_id','msisdn','sim_serial','plan_expiry_at','is_recharged','is_active'];
    protected $casts = ['plan_expiry_at'=>'date','is_recharged'=>'bool','is_active'=>'bool'];

    public function carrier() { 
        return $this->belongsTo(Carrier::class); 
    }

    public function scopeExpiryFrom($q,$d){ 
        return $q->whereDate('plan_expiry_at','>=',$d); 
    }

    public function scopeExpiryTo($q,$d){ 
        return $q->whereDate('plan_expiry_at','<=',$d); 
    }
}
