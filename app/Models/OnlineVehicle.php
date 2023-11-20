<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_user_id',
        'barcode_no',
        'vehicle_type_id',
        'vehicle_name',
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
