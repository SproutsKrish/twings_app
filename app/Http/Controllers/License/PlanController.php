<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Period;
use Illuminate\Support\Facades\Validator;

use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlanController extends BaseController
{

    //Licence Management Plan List
    public function index()
    {
        $plans = Plan::join('packages as b', 'plans.package_id', '=', 'b.id')
            ->join('periods as c', 'plans.period_id', '=', 'c.id')
            ->select('plans.id', 'b.package_name', 'c.period_name', 'c.period_days')
            ->get();

        if ($plans->isEmpty()) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $plans, "status_code" => 200];
            return response()->json($response, 200);
        }
    }

    //Vehicle Management Plan List
    public function user_plan_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $data = User::find($user_id);
        $role_id = $data->role_id;

        $plans = Plan::select('plans.id', 'packages.package_name', 'periods.period_days')
            ->join('packages', 'plans.package_id', '=', 'packages.id')
            ->join('periods', 'plans.period_id', '=', 'periods.id')
            ->whereIn('plans.id', function ($query) use ($role_id, $data) {
                $query->select('points.plan_id')
                    ->from('points')
                    ->where('total_point', '>=', 1)
                    ->where('point_type_id', 1);

                if ($role_id == 4) {
                    $query->where('dealer_id', $data->dealer_id)
                        ->whereNull('subdealer_id');
                } elseif ($role_id == 5) {
                    $query->where('subdealer_id', $data->subdealer_id);
                }
            })
            ->get();

        if ($plans->isEmpty()) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $plans, "status_code" => 200];
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
