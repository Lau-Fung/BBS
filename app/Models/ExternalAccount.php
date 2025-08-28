<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalAccount extends Model
{
    protected $casts = ['secret_enc' => 'encrypted']; // Laravel native encrypted cast
}
