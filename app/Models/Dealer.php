<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dealer_company',
        'dealer_name',
        'dealer_email',
        'dealer_mobile',
        'dealer_address',

        'dealer_logo',
        'dealer_limit',
        'dealer_city',
        'dealer_state',
        'dealer_pincode',

        'country_id',
        'country_name',
        'timezone_name',
        'timezone_offset',
        'timezone_minutes',

        'admin_id',
        'distributor_id',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
