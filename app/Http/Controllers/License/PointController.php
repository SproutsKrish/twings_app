<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\License;
use Illuminate\Support\Facades\Validator;

use App\Models\Point;
use App\Models\LicenseTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PointController extends BaseController
{

    public function index()
    {
        $points = Point::all();

        if ($points->isEmpty()) {
            return $this->sendError('No Points Found');
        }

        return $this->sendSuccess($points);
    }

    public function store(Request $request)
    {
        $role_id = $request->input('role_id');

        $created_by = $request->input('created_by');

        $admin_id = $request->input('admin_id');
        $distributor_id = $request->input('distributor_id');
        $dealer_id = $request->input('dealer_id');
        $subdealer_id = $request->input('subdealer_id');

        $plan_id = $request->input('plan_id');
        $point_type_id = $request->input('point_type_id');
        $total_point = $request->input('total_point');

        switch ($role_id) {
            case $role_id == 1:
                $admin_id = $request->input('user_id');

                $result = Point::where('admin_id', $admin_id)
                    ->where('distributor_id', null)
                    ->where('dealer_id', null)
                    ->where('subdealer_id', null)
                    ->where('plan_id', $plan_id)
                    ->where('point_type_id', $point_type_id)
                    ->where('status', 1)
                    ->first();

                if (!empty($result)) {
                    $result->total_point = $result->total_point + $request->input('total_point');
                    $result->save();
                    $transaction_head = new LicenseTransaction();
                    $transaction_head->point_id = $result->id;
                    $transaction_head->plan_quantity = $request->input('total_point');
                    $transaction_head->created_by = $request->input('created_by');
                    $transaction_head->ip_address = $request->input('ip_address');
                    $transaction_head->save();

                    if ($point_type_id == 1) {
                        for ($i = 1; $i <= $request->input('total_point'); $i++) {
                            $maxId = License::max('id');
                            if (!$maxId) {
                                $maxId = 1;
                                $maxId = "0000" . $maxId;
                            } else if ($maxId >= 1 && $maxId <= 8) {
                                $maxId += 1;
                                $maxId = "0000" . $maxId;
                            } else if ($maxId >= 9 &&  $maxId <= 98) {
                                $maxId += 1;
                                $maxId = "000" . $maxId;
                            } else if ($maxId >= 99 &&  $maxId <= 998) {
                                $maxId += 1;
                                $maxId = "00" . $maxId;
                            } else if ($maxId >= 999 &&  $maxId <= 9998) {
                                $maxId += 1;
                                $maxId = "0" . $maxId;
                            } else {
                                $maxId += 1;
                            }
                            $prefix = "GPS";
                            $date = Carbon::now()->format('Y');
                            $license_no = $prefix . $date . $maxId;
                            License::create(
                                ['license_no' => $license_no, 'plan_id' => $plan_id, 'admin_id' => $admin_id, 'created_by' => $created_by]
                            );
                        }
                    }
                    return $this->sendSuccess("New Point Added Successfully");
                } else {

                    $point['admin_id'] = $admin_id;
                    $point['point_type_id'] = $point_type_id;
                    $point['plan_id'] = $plan_id;
                    $point['total_point'] = $total_point;
                    $point['created_by'] = $created_by;
                    $point = new Point($point);
                    $point->save();

                    $transaction_head = new LicenseTransaction();
                    $transaction_head->point_id = $point->id;
                    $transaction_head->plan_quantity = $request->input('total_point');
                    $transaction_head->created_by = $request->input('created_by');
                    $transaction_head->ip_address = $request->input('ip_address');
                    $transaction_head->save();

                    if ($point_type_id == 1) {
                        for ($i = 1; $i <= $request->input('total_point'); $i++) {
                            $maxId = License::max('id');
                            if (!$maxId) {
                                $maxId = 1;
                                $maxId = "0000" . $maxId;
                            } else if ($maxId >= 1 && $maxId <= 8) {
                                $maxId += 1;
                                $maxId = "0000" . $maxId;
                            } else if ($maxId >= 9 &&  $maxId <= 98) {
                                $maxId += 1;
                                $maxId = "000" . $maxId;
                            } else if ($maxId >= 99 &&  $maxId <= 998) {
                                $maxId += 1;
                                $maxId = "00" . $maxId;
                            } else if ($maxId >= 999 &&  $maxId <= 9998) {
                                $maxId += 1;
                                $maxId = "0" . $maxId;
                            } else {
                                $maxId += 1;
                            }
                            $prefix = "SWTLIC";
                            $date = Carbon::now()->format('Y');
                            $license_no = $prefix . $date . $maxId;
                            License::create(['license_no' => $license_no, 'plan_id' => $plan_id, 'admin_id' => $admin_id, 'created_by' => $created_by]);
                        }
                    }
                    return $this->sendSuccess("New Point Added Successfully");
                }

                break;
            case $role_id == 2:
                $distributor_id = $request->input('user_id');

                $result = Point::where('total_point', '>=', $request->input('total_point'))
                    ->where('admin_id', $admin_id)
                    ->where('distributor_id', null)
                    ->where('dealer_id', null)
                    ->where('subdealer_id', null)
                    ->where('plan_id', $plan_id)
                    ->where('point_type_id', $point_type_id)
                    ->where('status', 1)
                    ->first();

                if (!empty($result)) {
                    $result->total_point = $result->total_point - $request->input('total_point');
                    $result->save();

                    $result = Point::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('plan_id', $plan_id)
                        ->where('point_type_id', $point_type_id)
                        ->where('status', 1)
                        ->first();

                    if (!empty($result)) {
                        $result->total_point = $result->total_point + $request->input('total_point');
                        $result->save();
                        $transaction_head = new LicenseTransaction();
                        $transaction_head->point_id = $result->id;
                        $transaction_head->plan_quantity = $request->input('total_point');
                        $transaction_head->created_by = $request->input('created_by');
                        $transaction_head->ip_address = $request->input('ip_address');
                        $transaction_head->save();

                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', null)
                                ->where('dealer_id', null)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update(
                                    ['distributor_id' => $distributor_id],
                                    ['created_by' => $created_by]
                                );
                        }

                        return $this->sendSuccess("New Point Added Successfully");
                    } else {

                        $point['admin_id'] = $admin_id;
                        $point['distributor_id'] = $distributor_id;
                        $point['point_type_id'] = $point_type_id;
                        $point['plan_id'] = $plan_id;
                        $point['total_point'] = $total_point;
                        $point['created_by'] = $created_by;
                        $point = new Point($point);
                        $point->save();

                        $transaction_head = new LicenseTransaction();
                        $transaction_head->point_id = $point->id;
                        $transaction_head->plan_quantity = $request->input('total_point');
                        $transaction_head->created_by = $request->input('created_by');
                        $transaction_head->ip_address = $request->input('ip_address');
                        $transaction_head->save();

                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', null)
                                ->where('dealer_id', null)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update([
                                    'distributor_id' => $distributor_id,
                                    'created_by' => $request->input('created_by')
                                ]);
                        }
                        return $this->sendSuccess("New Point Added Successfully");
                    }
                } else {
                    $response = ["success" => false, "message" => "Requested Point Not Available", "status_code" => 403];
                    return response()->json($response, 403);
                }
                break;
            case $role_id == 3:

                $dealer_id = $request->input('user_id');

                $result = Point::where('total_point', '>=', $request->input('total_point'))
                    ->where('admin_id', $admin_id)
                    ->where('distributor_id', $distributor_id)
                    ->where('dealer_id', null)
                    ->where('subdealer_id', null)
                    ->where('plan_id', $plan_id)
                    ->where('point_type_id', $point_type_id)
                    ->where('status', 1)
                    ->first();

                // return response()->json($result);

                if (!empty($result)) {
                    $result->total_point = $result->total_point - $request->input('total_point');
                    $result->save();

                    $result = Point::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', $dealer_id)
                        ->where('subdealer_id', null)
                        ->where('plan_id', $plan_id)
                        ->where('point_type_id', $point_type_id)
                        ->where('status', 1)
                        ->first();
                    if (!empty($result)) {
                        $result->total_point = $result->total_point + $request->input('total_point');
                        $result->save();
                        $transaction_head = new LicenseTransaction();
                        $transaction_head->point_id = $result->id;
                        $transaction_head->plan_quantity = $request->input('total_point');
                        $transaction_head->created_by = $request->input('created_by');
                        $transaction_head->ip_address = $request->input('ip_address');
                        $transaction_head->save();

                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', $distributor_id)
                                ->where('dealer_id', null)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update([
                                    'dealer_id' => $dealer_id,
                                    'created_by' => $request->input('created_by')
                                ]);
                        }
                        return $this->sendSuccess("New Point Added Successfully");
                    } else {

                        $point['admin_id'] = $admin_id;
                        $point['distributor_id'] = $distributor_id;
                        $point['dealer_id'] = $dealer_id;
                        $point['point_type_id'] = $point_type_id;
                        $point['plan_id'] = $plan_id;
                        $point['total_point'] = $total_point;
                        $point['created_by'] = $created_by;
                        $point = new Point($point);
                        $point->save();

                        $transaction_head = new LicenseTransaction();
                        $transaction_head->point_id = $point->id;
                        $transaction_head->plan_quantity = $request->input('total_point');
                        $transaction_head->created_by = $request->input('created_by');
                        $transaction_head->ip_address = $request->input('ip_address');
                        $transaction_head->save();

                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', $distributor_id)
                                ->where('dealer_id', null)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update(
                                    ['dealer_id' => $dealer_id],
                                    ['created_by' => $created_by]
                                );
                        }

                        return $this->sendSuccess("New Point Added Successfully");
                    }
                } else {
                    $response = ["success" => false, "message" => "Requested Point Not Available", "status_code" => 403];
                    return response()->json($response, 403);
                }
                break;
            case $role_id == 4:
                $subdealer_id = $request->input('user_id');

                $result = Point::where('total_point', '>=', $request->input('total_point'))
                    ->where('admin_id', $admin_id)
                    ->where('distributor_id', $distributor_id)
                    ->where('dealer_id', $dealer_id)
                    ->where('subdealer_id', null)
                    ->where('plan_id', $plan_id)
                    ->where('point_type_id', $point_type_id)
                    ->where('status', 1)
                    ->first();

                if (!empty($result)) {
                    $result->total_point = $result->total_point - $request->input('total_point');
                    $result->save();

                    $result = Point::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', $dealer_id)
                        ->where('subdealer_id', $subdealer_id)
                        ->where('plan_id', $plan_id)
                        ->where('point_type_id', $point_type_id)
                        ->where('status', 1)
                        ->first();
                    if (!empty($result)) {
                        $result->total_point = $result->total_point + $request->input('total_point');
                        $result->save();
                        $transaction_head = new LicenseTransaction();
                        $transaction_head->point_id = $result->id;
                        $transaction_head->plan_quantity = $request->input('total_point');
                        $transaction_head->created_by = $request->input('created_by');
                        $transaction_head->ip_address = $request->input('ip_address');
                        $transaction_head->save();


                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', $distributor_id)
                                ->where('dealer_id', $dealer_id)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update([
                                    'subdealer_id' => $subdealer_id,
                                    'created_by' => $request->input('created_by')
                                ]);
                        }

                        return $this->sendSuccess("New Point Added Successfully");
                    } else {

                        $point['admin_id'] = $admin_id;
                        $point['distributor_id'] = $distributor_id;
                        $point['dealer_id'] = $dealer_id;
                        $point['subdealer_id'] = $subdealer_id;
                        $point['point_type_id'] = $point_type_id;
                        $point['plan_id'] = $plan_id;
                        $point['total_point'] = $total_point;
                        $point['created_by'] = $created_by;
                        $point = new Point($point);
                        $point->save();

                        $transaction_head = new LicenseTransaction();
                        $transaction_head->point_id = $point->id;
                        $transaction_head->plan_quantity = $request->input('total_point');
                        $transaction_head->created_by = $request->input('created_by');
                        $transaction_head->ip_address = $request->input('ip_address');
                        $transaction_head->save();

                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', $distributor_id)
                                ->where('dealer_id', $dealer_id)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update(
                                    ['subdealer_id' => $subdealer_id],
                                    ['created_by' => $created_by]
                                );
                        }

                        return $this->sendSuccess("New Point Added Successfully");
                    }
                } else {
                    $response = ["success" => false, "message" => "Requested Point Not Available", "status_code" => 403];
                    return response()->json($response, 403);
                }
                break;
            default:
        }
    }

    public function point_stock_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_id = $request->input('role_id');

        $admin_id = auth()->user()->admin_id;
        $distributor_id = auth()->user()->distributor_id;
        $dealer_id = auth()->user()->dealer_id;
        $subdealer_id = auth()->user()->subdealer_id;

        if ($role_id == 2) {
            $result = DB::table('points as a')
                ->select('c.point_type', 'd.package_code', 'd.package_name', 'e.period_name', 'e.period_days', 'f.admin_name as name', 'a.total_point')
                ->join('plans as b', 'a.plan_id', '=', 'b.id')
                ->join('point_types as c', 'a.point_type_id', '=', 'c.id')
                ->join('packages as d', 'd.id', '=', 'b.package_id')
                ->join('periods as e', 'e.id', '=', 'b.period_id')
                ->join('admins as f', 'f.id', '=', 'a.admin_id')
                ->where('a.admin_id', $admin_id)
                ->where('a.distributor_id', $distributor_id)
                ->where('a.dealer_id', $dealer_id)
                ->where('a.subdealer_id', $subdealer_id)
                ->get();
        } else  if ($role_id == 3) {
            $result = DB::table('points as a')
                ->select('c.point_type', 'd.package_code', 'd.package_name', 'e.period_name', 'e.period_days', 'f.distributor_name as name', 'a.total_point')
                ->join('plans as b', 'a.plan_id', '=', 'b.id')
                ->join('point_types as c', 'a.point_type_id', '=', 'c.id')
                ->join('packages as d', 'd.id', '=', 'b.package_id')
                ->join('periods as e', 'e.id', '=', 'b.period_id')
                ->join('distributors as f', 'f.id', '=', 'a.distributor_id')
                ->where('a.admin_id', $admin_id)
                ->where('a.distributor_id', $distributor_id)
                ->where('a.dealer_id', $dealer_id)
                ->where('a.subdealer_id', $subdealer_id)
                ->get();
        } else  if ($role_id == 4) {
            $result = DB::table('points as a')
                ->select('c.point_type', 'd.package_code', 'd.package_name', 'e.period_name', 'e.period_days', 'f.dealer_name as name', 'a.total_point')
                ->join('plans as b', 'a.plan_id', '=', 'b.id')
                ->join('point_types as c', 'a.point_type_id', '=', 'c.id')
                ->join('packages as d', 'd.id', '=', 'b.package_id')
                ->join('periods as e', 'e.id', '=', 'b.period_id')
                ->join('dealers as f', 'f.id', '=', 'a.dealer_id')
                ->where('a.admin_id', $admin_id)
                ->where('a.distributor_id', $distributor_id)
                ->where('a.dealer_id', $dealer_id)
                ->where('a.subdealer_id', $subdealer_id)
                ->get();
        } else  if ($role_id == 5) {
            $result = DB::table('points as a')
                ->select('c.point_type', 'd.package_code', 'd.package_name', 'e.period_name', 'e.period_days', 'f.subdealer_name as name', 'a.total_point')
                ->join('plans as b', 'a.plan_id', '=', 'b.id')
                ->join('point_types as c', 'a.point_type_id', '=', 'c.id')
                ->join('packages as d', 'd.id', '=', 'b.package_id')
                ->join('periods as e', 'e.id', '=', 'b.period_id')
                ->join('sub_dealers as f', 'f.id', '=', 'a.subdealer_id')
                ->where('a.admin_id', $admin_id)
                ->where('a.distributor_id', $distributor_id)
                ->where('a.dealer_id', $dealer_id)
                ->where('a.subdealer_id', $subdealer_id)
                ->get();
        }



        if (empty($result)) {
            return $this->sendError('No Users Found');
        }

        return $this->sendSuccess($result);
    }
}
