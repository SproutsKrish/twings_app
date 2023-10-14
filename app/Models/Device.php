<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Device extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'device_make_id',
        'device_model_id',
        'device_imei_no',
        'ccid',
        'uid',
        'start_date',
        'end_date',
        'sensor_name',
        'purchase_date',
        'description',
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

    public function scopeAvailableForUser($query, $user)
    {
        return $query
            ->where('client_id', null)
            ->where('status', '1')
            ->where(function ($query) use ($user) {
                $query
                    ->where('dealer_id', $user->dealer_id)
                    ->Where(function ($query) use ($user) {
                        $query->where('subdealer_id', $user->subdealer_id);
                    });
            });
    }


    public static function validationRules($id = null)
    {
        $rules = [
            'supplier_id' => 'required',
            'device_make_id' => 'required',
            'device_model_id' => 'required',
            'device_imei_no' => [
                'required',
                Rule::unique('devices')->ignore($id),
            ],
        ];

        return $rules;
    }
}
