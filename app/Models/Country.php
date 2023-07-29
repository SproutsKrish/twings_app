<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'country_name',
        'short_name',
        'phone_code',
        'timezone_name',
        'timezone_offset',
        'timezone_minutes',

        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address'
    ];
}
