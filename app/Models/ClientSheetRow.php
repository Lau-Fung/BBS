<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientSheetRow extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'data_package_type','sim_type','sim_number','imei','plate',
        'installed_on','year_model','company_manufacture','device_type',
        'air','mechanic','tracking', 'system_type',
        'calibration','color','crm_integration','technician',
        'vehicle_serial_number','vehicle_weight',
        'user','notes',
    ];

    protected $casts = [
        'installed_on' => 'date',
        'air'          => 'boolean',
        'mechanic'     => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}

