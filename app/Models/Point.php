<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'point_type_id',
        'plan_id',
        'total_point',
        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
