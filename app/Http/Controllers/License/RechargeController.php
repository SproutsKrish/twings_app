<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\License;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Point;

use Carbon\Carbon;

class RechargeController extends BaseController
{
    public function recharge(Request $request)
    {
        $license_no = $request->input('license_no');

        $license = License::where('license_no', $license_no)->first();
        if ($license) {
            // Update the status to 0
            $license->status = 0;
            $license->save();
        }

        $plan_id = $request->input('plan_id');
        $plan = Plan::find($plan_id);
        $period_id = $plan->period_id;
        $period = Period::find($period_id);

        $start_date = $request->input('expiry_date');
        $expiry_date = $request->input('expiry_date');

        $admin_id = $request->input('admin_id');
        $distributor_id = $request->input('distributor_id');

        $dealer_id = $request->input('dealer_id');
        $subdealer_id = $request->input('subdealer_id');
        $client_id = $request->input('client_id');
        $vehicle_id = $request->input('vehicle_id');
        $created_by = $request->input('created_by');

        $ip_address = $request->input('ip_address');



        if ($client_id != null && $dealer_id != null && $subdealer_id == null) {
            $license = new License();
            $license->license_no = $license_no;
            $license->plan_id = $plan_id;
            $license->vehicle_id = $vehicle_id;
            $license->start_date = $start_date;
            $license->expiry_date = $expiry_date;
            $license->admin_id = $admin_id;
            $license->distributor_id = $distributor_id;
            $license->dealer_id = $dealer_id;
            $license->client_id = $client_id;

            $license->created_by = $created_by;
            $license->ip_address = $ip_address;
            $license->save();
            $point = Point::where('dealer_id', $dealer_id)
                ->where('subdealer_id', null)
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('plan_id', $plan_id)
                ->where('point_type_id', '2')
                ->first();
            if ($point) {
                $point->total_point = $point->total_point - 1;
                $point->save();
            }
            return $this->sendSuccess("Renewed Successfully");
        } else if ($client_id != null && $dealer_id != null && $subdealer_id != null) {
            $license = new License();
            $license->license_no = $license_no;
            $license->plan_id = $plan_id;
            $license->vehicle_id = $vehicle_id;
            $license->start_date = $start_date;
            $license->expiry_date = $expiry_date;
            $license->admin_id = $admin_id;
            $license->distributor_id = $distributor_id;
            $license->dealer_id = $dealer_id;
            $license->client_id = $client_id;
            $license->created_by = $created_by;
            $license->ip_address = $ip_address;
            $license->subdealer_id = $subdealer_id;
            $license->save();

            $point = Point::where('dealer_id', $dealer_id)
                ->where('subdealer_id', $subdealer_id)
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('plan_id', $plan_id)
                ->where('point_type_id', '2')
                ->first();
            if ($point) {
                $point->total_point = $point->total_point - 1;
                $point->save();
            }
            return $this->sendSuccess("Renewed Successfully");
        }
    }
}
