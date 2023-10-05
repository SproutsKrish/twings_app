<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'device_make_id',
        'device_model_id',
        'device_imei_no',
        'ccid',
        'uid',
        'start_date',
        'end_date',
        'sensor_name',
        'purchase_date',
        'description',
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
