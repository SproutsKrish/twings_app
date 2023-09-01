<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "staffs";

    protected $fillable = [
        'staff_name',
        'staff_address',
        'user_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
