<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supplier_name',
        'supplier_email',
        'supplier_mobile_no',

        'supplier_address',
        'supplier_city',
        'supplier_state',

        'supplier_pincode',
        'supplier_country',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
