<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdleReport extends Model
{
    use HasFactory;

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
        'start_location',
        'end_location',
        'client_id',
        'updated_status',
    ];
}
