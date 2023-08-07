<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayBackHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'deviceimei',
        'lattitute',
        'longitute',
        'speed',
        'odometer',
        'angle',
        'device_datetime',
        'ignition',
        'ac_status',
        'packet_status',
        'packet_details',
        'client_id',
    ];
}
