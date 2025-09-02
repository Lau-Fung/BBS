<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    protected $fillable = ['name', 'manufacturer'];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
