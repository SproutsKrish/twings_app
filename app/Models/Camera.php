<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'camera_type_id',
        'camera_category_id',
        'camera_model_id',

        'serial_no',
        'id_no',
        'purchase_date',

        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'client_id',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
