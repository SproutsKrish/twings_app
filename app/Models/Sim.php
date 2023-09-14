<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sim extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'network_id',
        'sim_imei_no',
        'sim_mob_no1',
        'sim_mob_no2',
        'valid_from',
        'valid_to',
        'purchase_date',

        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'client_id',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
        'ip_address',
    ];
}
