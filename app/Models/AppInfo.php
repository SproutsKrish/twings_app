<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'app_description',
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
