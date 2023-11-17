<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_user_id',
        'sim_imei_no',
        'sim_mob_no1',
        'sim_mob_no2',
        'device_imei',
        'device_ccid',
        'device_uid',
        'vehicle_type_id',
        'vehicle_name',
        'description',
        'app_id',
        'app_name',
        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'status',
        'created_by',
        'updated_by',
        'ip_address',
    ];
}