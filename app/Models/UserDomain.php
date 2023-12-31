<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_name',
        'login_image',
        'created_by',
        'updated_by',
    ];
}
