<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'distributor_company',
        'distributor_name',
        'distributor_email',
        'distributor_mobile',
        'distributor_address',
        'distributor_logo',
        'distributor_limit',
        'distributor_city',
        'distributor_state',
        'distributor_pincode',
        'country_id',
        'country_name',
        'timezone_name',
        'timezone_offset',
        'timezone_minutes',
        'admin_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
