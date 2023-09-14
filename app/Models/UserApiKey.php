<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'domain_id',
        'api_key',
        'created_by',
        'updated_by',
    ];
}
