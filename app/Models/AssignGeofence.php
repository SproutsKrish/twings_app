<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignGeofence extends Model
{
    use HasFactory;

    protected $table = 'assign_geofence';

    // Specify the fillable columns to allow mass assignment
    protected $fillable = [
        'client_id',
        'vehicle_id',
        'geofence_id',
        'fence_type',
    ];
}
