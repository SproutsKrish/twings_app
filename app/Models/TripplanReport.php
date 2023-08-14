<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripplanReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'device_imei',
        'vehicle_id',
        'vehicle_name',
        'start_location',
        'end_location',
        'poc_number',
        'route_id',
        'route_name',
        'start_geofence_id',
        'end_geofence_id',
        'geofence_status',
        'trip_date',
        'trip_type',
        'parking_duration',
        'idle_duration',
        'start_odometer',
        'end_odometer',
        'distance_km',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'flag',
        'status',
    ];
}
