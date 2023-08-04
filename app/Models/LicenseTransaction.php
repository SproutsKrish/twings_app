<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'point_id',
        'plan_quantity',
        'status',
        'created_by',
        'updated_by',
        'ip_address',
    ];
}
