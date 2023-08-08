<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'vehicle_id', 'vehicle_name', 'device_imei',
        'parking_alert_time', 'idle_alert_time', 'speed_limit',
        'expected_mileage', 'idle_rpm', 'max_rpm',
        'temp_low', 'temp_high', 'fuel_fill_limit', 'fuel_dip_limit',
    ];
}
