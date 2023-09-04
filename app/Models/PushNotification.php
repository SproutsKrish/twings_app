<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_type',
        'mobile_model',
        'application_name',
        'server_key',
        'fcm_token',
        'access_token',
        'user_id',
        'role_id',
        'client_id',
    ];
}
