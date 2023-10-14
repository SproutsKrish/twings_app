<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Sim extends Model
{
    use HasFactory;
    // use SoftDeletes;


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
            'network_id' => 'required|max:255',
            'sim_imei_no' => [
                'required',
                Rule::unique('sims')->ignore($id),
            ],
            'sim_mob_no1' => [
                'required',
                Rule::unique('sims')->ignore($id),
            ],
        ];

        return $rules;
    }



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
