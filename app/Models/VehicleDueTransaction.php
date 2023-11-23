<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDueTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'total_amount',
        'vehicle_id',
        'due_amount',
        'created_by',
        'updated_by',
    ];
}
