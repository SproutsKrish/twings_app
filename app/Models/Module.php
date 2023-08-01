<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'module_name',
        'module_icon',
        'module_url',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
