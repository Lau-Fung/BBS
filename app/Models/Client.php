<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    protected $fillable = ['name','sector','subscription_type'];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    // assignments linked via vehicles
    public function assignments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Assignment::class,
            Vehicle::class,
            'client_id',    // Vehicle->client_id
            'vehicle_id',   // Assignment->vehicle_id
            'id',
            'id'
        );
    }
}
