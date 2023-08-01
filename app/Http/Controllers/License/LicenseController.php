<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\License;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Point;
use Carbon\Carbon;

class LicenseController extends BaseController
{
    public function index()
    {
        $licenses = License::all();

        if ($licenses->isEmpty()) {
            return $this->sendError('No Licenses Found');
        }

        return $this->sendSuccess($licenses);
    }

    public function store(Request $request)
    {
        $admin_id = $request->input('admin_id');
        $distributor_id = $request->input('distributor_id');

        $dealer_id = $request->input('dealer_id');
        $subdealer_id = $request->input('subdealer_id');
        $client_id = $request->input('client_id');

        $plan_id = $request->input('plan_id');

        //dealer license to client
        if ($client_id != null && $dealer_id != null && $subdealer_id == null) {

            $result = Point::where('balance_point', '>=', 1)
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', null)
                ->where('plan_id', $plan_id)
                ->where('status', 1)
                ->first();

            if (!empty($result)) {
                $result->balance_point = $result->balance_point - 1;

                if ($result->balance_point == 0) {
                    $result->status = 0;
                }
                $result->save();

                $point = new License($request->all());

                $plan = Plan::find($plan_id);
                $period_id = $plan->period_id;
                $period = Period::find($period_id);;

                $start_date = Carbon::now();
                $newDateTime = $start_date->addDays($period->period_days);
                $point->expiry_date = $newDateTime->format('Y-m-d H:i:s');
                $point->start_date = Carbon::now();

                $point->save();
                return $this->sendSuccess("License Created Successfully");
            } else {
                return $this->sendError("License Created Failed");
            }
        }
        //subdealer license to client
        else if ($client_id != null && $dealer_id != null && $subdealer_id != null) {

            $result = Point::where('balance_point', '>=', 1)
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', $subdealer_id)
                ->where('plan_id', $plan_id)
                ->where('status', 1)
                ->first();

            if (!empty($result)) {
                $result->balance_point = $result->balance_point - 1;

                if ($result->balance_point == 0) {
                    $result->status = 0;
                }
                $result->save();

                $point = new License($request->all());

                $plan = Plan::find($plan_id);
                $period_id = $plan->period_id;
                $period = Period::find($period_id);;

                $start_date = Carbon::now();
                $newDateTime = $start_date->addDays($period->period_days);
                $point->expiry_date = $newDateTime->format('Y-m-d H:i:s');
                $point->start_date = Carbon::now();

                $point->save();
                return $this->sendSuccess("License Created Successfully");
            } else {
                return $this->sendError("License Created Failed");
            }
        } else {
            return $this->sendError('Failed to insert license.', [], 500);
        }
    }
}
