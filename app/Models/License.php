<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'license_no',
        'plan_id',
        'vehicle_id',
        'start_date',
        'expiry_date',
        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'client_id',
        'created_by',
        'updated_by',
    ];
}
