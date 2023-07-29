<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vehicle_type_id',
        'vehicle_name',
        'vehicle_make',
        'vehicle_model',
        'vehicle_year',
        'device_id',
        'device_imei',
        'sim_id',
        'sim_mob_no',
        'insurance_company',
        'insurance_number',
        'insurance_start_date',
        'insurance_expiry_date',
        'registration_number',
        'chassis_number',
        'engine_number',
        'ownership_type',
        'fc_date',
        'installation_date',
        'expire_date',
        'extend_date',
        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'client_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
