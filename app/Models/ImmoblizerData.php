<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImmoblizerData extends Model
{
    use HasFactory;
    protected $table = "twings.immoblizer_data";

    protected $fillable = [
        'client_id',
        'user_id',
        'deviceimei',
        'vehicle_id',
        'status',
        'completed_status',
        'address',
        'device_port',
        'device_name',
        'dealer_id',
        'subdealer_id',
        'created_by',
        'created_at',
        'updated_at',
        'created_on'
    ];
}
