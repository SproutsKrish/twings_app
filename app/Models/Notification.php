<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'ac_on', 'ac_off', 'ignition_on', 'ignition_off',
        'speed_alert', 'route_deviation', 'temperature_alert',
        'sos_alert', 'geofence_in_circle', 'geofence_out_circle',
        'acceleration', 'braking', 'cornering', 'speed_breaker_bump',
        'accident', 'fuel_dip', 'fuel_fill', 'power_off',
        'hub_in_circle', 'hub_out_circle', 'low_battery',
    ];
}
