<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\License;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Point;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $point_type_id = $request->input('point_type_id');

        $maxId = License::max('id');
        if (!$maxId)
            $maxId = 1;
        else
            $maxId += 1;
        $date = Carbon::now()->format('Ymd');

        $request->merge(['license_no' => $date . $maxId]);


        //dealer license to client
        if ($client_id != null && $dealer_id != null && $subdealer_id == null && $point_type_id == 1) {

            $result = Point::where('total_point', '>=', 1)
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', null)
                ->where('plan_id', $plan_id)
                ->where('point_type_id', $point_type_id)
                ->where('status', 1)
                ->first();

            if (!empty($result)) {
                $result->total_point = $result->total_point - 1;
                $result->save();

                $plan_id = $result->plan_id;

                $plan = Plan::find($plan_id);
                $period_id = $plan->period_id;
                $period = Period::find($period_id);

                $point = new License($request->all());

                $start_date = Carbon::now();
                $point->start_date = Carbon::now();
                $newDateTime = $start_date->addDays($period->period_days);
                $point->expiry_date = $newDateTime->format('Y-m-d H:i:s');
                $point->save();
                return $this->sendSuccess("License Created Successfully");
            } else {
                return $this->sendError("License Created Failed");
            }
        }
        //subdealer license to client
        else if ($client_id != null && $dealer_id != null && $subdealer_id != null && $point_type_id == 1) {

            $result = Point::where('total_point', '>=', 1)
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', $subdealer_id)
                ->where('plan_id', $plan_id)
                ->where('point_type_id', $point_type_id)
                ->where('status', 1)
                ->first();

            if (!empty($result)) {
                $result->total_point = $result->total_point - 1;
                $result->save();

                $plan_id = $result->plan_id;

                $plan = Plan::find($plan_id);
                $period_id = $plan->period_id;
                $period = Period::find($period_id);

                $point = new License($request->all());


                // dd($request->all());

                $start_date = Carbon::now();
                $point->start_date = Carbon::now();
                $newDateTime = $start_date->addDays($period->period_days);
                $point->expiry_date = $newDateTime->format('Y-m-d H:i:s');
                $point->save();
                return $this->sendSuccess("License Created Successfully");
            } else {
                return $this->sendError("License Created Failed");
            }
        } else {
            return $this->sendError('Failed to insert license.', [], 500);
        }
    }

    public function user_license_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $plan_id = $request->input('plan_id');

        $data = User::find($user_id);
        $dealer_id = null;
        $subdealer_id = null;

        $role_id =  $data->role_id;

        if ($role_id == 4) {
            $dealer_id = $data->dealer_id;

            $licenses = DB::table('licenses as a')
                ->select('a.id', 'a.license_no')
                ->join('plans as b', 'b.id', '=', 'a.plan_id')
                ->join('periods as c', 'c.id', '=', 'b.period_id')
                ->join('packages as d', 'd.id', '=', 'b.package_id')
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', $subdealer_id)
                ->where('b.id', $plan_id)
                ->where('a.client_id', null)
                ->get();
        } else if ($role_id == 5) {
            $subdealer_id = $data->subdealer_id;

            $licenses = DB::table('licenses as a')
                ->select('a.id', 'a.license_no')
                ->join('plans as b', 'b.id', '=', 'a.plan_id')
                ->join('periods as c', 'c.id', '=', 'b.period_id')
                ->join('packages as d', 'd.id', '=', 'b.package_id')
                ->where('subdealer_id', $subdealer_id)
                ->where('b.id', $plan_id)
                ->where('a.client_id', null)
                ->get();
        }
        if (empty($licenses)) {
            $response = ["success" => false, "message" => "No Datas Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $licenses, "status_code" => 200];
            return response()->json($response, 200);
        }
    }
}
