<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sim extends Model
{
    public function carrier() { 
        return $this->belongsTo(Carrier::class); 
    }
}
