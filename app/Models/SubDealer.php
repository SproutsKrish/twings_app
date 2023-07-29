<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubDealer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'subdealer_company',
        'subdealer_name',
        'subdealer_email',
        'subdealer_mobile',
        'subdealer_address',

        'subdealer_logo',
        'subdealer_limit',
        'subdealer_city',
        'subdealer_state',
        'subdealer_pincode',

        'country_id',
        'country_name',
        'timezone_name',
        'timezone_offset',
        'timezone_minutes',

        'admin_id',
        'distributor_id',
        'dealer_id',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
