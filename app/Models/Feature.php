<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'feature_name',
        'status',
        'approval_status',
        'approved_at',
        'approved_by',
        'ip_address',
    ];
}
