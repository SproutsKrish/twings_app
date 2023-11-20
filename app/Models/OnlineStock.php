<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class OnlineStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'sim_id',
        'device_id',
        'barcode_no',
        'sim_imei',
        'sim_mob_no1',
        'sim_mob_no2',
        'device_imei',
        'device_ccid',
        'device_uid',
        'admin_id',
        'distributor_id',
        'dealer_id',
        'subdealer_id',
        'status',
        'created_by',
        'updated_by',
        'ip_address',
    ];


    public static function validationCreateRulesSim()
    {
        $rules = [
            'network_id' => 'required',
            'sim_imei_no' => [
                'required',
                Rule::unique('sims'),
            ],
            'sim_mob_no1' => [
                'required',
                Rule::unique('sims'),
            ],
        ];

        return $rules;
    }

    public static function validationCreateRulesDevice()
    {
        $rules = [
            'supplier_id' => 'required',
            'device_make_id' => 'required',
            'device_model_id' => 'required',
            'device_imei_no' => [
                'required',
                Rule::unique('devices'),
            ],
        ];

        return $rules;
    }
}
