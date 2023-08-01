<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'package_id',
        'period_id',
        'status',
        'approval_status',
        'approved_at',
        'approved_by',
        'ip_address',
    ];
}
