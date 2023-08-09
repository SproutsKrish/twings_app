<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteDeviation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'route_id',
        'route_name',
        'vehicle_imei',
        'vehicle_name',
        'route_deviate_outtime',
        'route_deviate_intime',
        'route_out_location',
        'route_in_location',
        'route_out_lat',
        'route_out_lng',
        'route_in_lat',
        'route_in_lng',
        'location_status',
    ];
}
