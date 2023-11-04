<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveData extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'vehicle_name',
        'vehicle_current_status',
        'vehicle_status',
        'deviceimei',
        'lattitute',
        'longitute',
        'ignition',
        'ac_status',
        'speed',
        'angle',
        'odometer',
        'device_updatedtime',
        'temperature',
        'device_battery_volt',
        'vehicle_battery_volt',
        'last_ignition_on_time',
        'last_ignition_off_time',
        'fuel_litre',
        'vehicle_sleep',
        'imobilizer_status',
        'altitude',
        'gpssignal',
        'gsm_status',
        'rpm_value',
        'device_type_id',
        'sec_engine_status',
    ];
}
