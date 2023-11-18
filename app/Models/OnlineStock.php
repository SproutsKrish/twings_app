<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode_no',
        'sim_imei',
        'sim_mob_no1',
        'sim_mob_no2',
        'device_imei',
        'device_ccid',
        'device_uid',
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
