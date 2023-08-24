<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'admin_company',
        'admin_name',
        'admin_email',
        'admin_mobile',
        'admin_address',

        'admin_logo',
        'admin_limit',
        'admin_city',
        'admin_state',
        'admin_pincode',

        'user_id',

        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
