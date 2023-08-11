<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    use HasFactory;


    protected $table = 'geofence';

    // Specify the fillable columns to allow mass assignment
    protected $fillable = [
        'location_short_name',
        'lat',
        'lng',
        'circle_size',
        'radius',
        'client_id',
        'active_code'
    ];
}
