<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_company',
        'client_name',
        'client_email',
        'client_mobile',
        'client_address',

        'client_logo',
        'client_limit',
        'client_city',
        'client_state',
        'client_pincode',

        'user_id',

        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
