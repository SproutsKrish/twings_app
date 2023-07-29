<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'policy_no',
        'insurance_company_name',
        'insurance_type',
        'insurance_start_date',
        'insurance_expiry_date',
        'insurance_front_image',
        'insurance_back_image',
        'fitness_certificate_expiry_date',
        'fitness_front_image',
        'fitness_back_image',
        'tax_expiry_date',
        'tax_front_image',
        'tax_back_image',
        'permit_expiry_date',
        'permit_front_image',
        'permit_back_image',
        'rc_expiry_date',
        'rc_front_image',
        'rc_back_image',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
