<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_short_name',
        'latitude',
        'longitude',
        'circle_size',
        'radius',
        'client_id',
        'active_code',
    ];
}
