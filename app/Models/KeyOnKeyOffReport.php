<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyOnKeyOffReport extends Model
{
    use HasFactory;

    protected $table = 'keyoff_keyon_reports';

    protected $fillable = [
        'id',
        'flag',
        's_lat',
        's_lng',
        'start_day',
        'end_day',
        'device_no',
        'vehicle_id',
        'total_km',
        'e_lat',
        'e_lng',
        'type_id',
        'fuel_usage',
        'fuel_filled',
        'initial_ltr',
        'end_ltr',
        'car_battery',
        'device_battery',
        'start_odometer',
        'end_odometer',
        'real_start_odo',
        'real_end_odo',
        'start_location',
        'end_location',
        'client_id',
    ];
}
