<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerConfiguration extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'user_id',
        'client_id',
        'db_name',
        'user_name',
        'password',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
