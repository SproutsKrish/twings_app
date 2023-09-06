<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanController extends BaseController
{
    public function index()
    {
        $plans = DB::table('plans as a')
            ->join('packages as b', 'a.package_id', '=', 'b.id')
            ->join('periods as c', 'a.period_id', '=', 'c.id')
            ->select('a.id', 'b.package_name', 'c.period_name', 'c.period_days')
            ->get();

        if ($plans->isEmpty()) {
            return $this->sendError('No Plans Found');
        }

        return $this->sendSuccess($plans);
    }

    public function user_plan_list(Request $request)
    {
        $user_id = $request->input('user_id');

        $data = User::find($user_id);
        $role_id = $data->role_id;

        $dealer_id = null;
        $subdealer_id = null;
        if ($role_id == 4) {
            $dealer_id = $data->dealer_id;

            $results = DB::table('plans as a')
                ->select('a.id', 'b.package_name', 'c.period_days')
                ->join('packages as b', 'a.package_id', '=', 'b.id')
                ->join('periods as c', 'a.period_id', '=', 'c.id')
                ->whereIn('a.id', function ($query) use ($dealer_id) {
                    $query->select('plan_id')
                        ->from('points')
                        ->where('total_point', '>=', 1)
                        ->where('point_type_id', 1)
                        ->where('dealer_id', $dealer_id)
                        ->whereNull('subdealer_id');
                })
                ->get();
        } else if ($role_id == 5) {
            $subdealer_id = $data->subdealer_id;

            $results = DB::table('plans as a')
                ->select('a.id', 'b.package_name', 'c.period_days')
                ->join('packages as b', 'a.package_id', '=', 'b.id')
                ->join('periods as c', 'a.period_id', '=', 'c.id')
                ->whereIn('a.id', function ($query) use ($subdealer_id) {
                    $query->select('plan_id')
                        ->from('points')
                        ->where('total_point', '>=', 1)
                        ->where('point_type_id', 1)
                        ->where('subdealer_id', $subdealer_id);
                })
                ->get();
        }
        if (empty($results)) {
            $response = ["success" => false, "message" => "No Datas Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $results, "status_code" => 200];
            return response()->json($response, 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|max:255',
            'period_id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $plan = new Plan($request->all());
        if ($plan->save()) {
            return $this->sendSuccess("Plan Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Plan');
        }
    }

    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return $this->sendError('Plan Not Found');
        }

        return $this->sendSuccess($plan);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return $this->sendError('Plan Not Found');
        }

        $validator = Validator::make($request->all(), [
            'package_id' => 'required|max:255',
            'period_id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($plan->update($request->all())) {
            return $this->sendSuccess("Plan Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Plan');
        }
    }

    public function destroy(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return $this->sendError('Plan Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $plan->status = 0;
        $plan->deleted_by = $request->deleted_by;
        $plan->save();
        if ($plan->delete()) {
            return $this->sendSuccess('Plan Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Plan');
        }
    }
}
