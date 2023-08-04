<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Point;
use App\Models\LicenseTransaction;

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
        $super_admin_id = $request->input('created_by');
        $admin_id = $request->input('admin_id');
        $distributor_id = $request->input('distributor_id');
        $dealer_id = $request->input('dealer_id');
        $subdealer_id = $request->input('subdealer_id');
        $plan_id = $request->input('plan_id');
        $point_type_id = $request->input('point_type_id');

        //Super Admin to Admin
        if ($super_admin_id == '1' && $admin_id != null && $distributor_id == null && $dealer_id == null && $subdealer_id == null) {

            $result = Point::where('admin_id', $admin_id)
                ->where('distributor_id', null)
                ->where('dealer_id', null)
                ->where('subdealer_id', null)
                ->where('created_by', $super_admin_id)
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
                return $this->sendSuccess("New Point Added Successfully");
            } else {
                $point = new Point($request->all());
                $point->save();
                $transaction_head = new LicenseTransaction();
                $transaction_head->point_id = $point->id;
                $transaction_head->plan_quantity = $request->input('total_point');
                $transaction_head->created_by = $request->input('created_by');
                $transaction_head->ip_address = $request->input('ip_address');
                $transaction_head->save();
                return $this->sendSuccess("New Point Added Successfully");
            }
        }
        //Admin to Distributor
        else  if ($admin_id != null && $distributor_id != null && $dealer_id == null && $subdealer_id == null) {

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
                    return $this->sendSuccess("New Point Added Successfully");
                } else {

                    $point = new Point($request->all());
                    $point->save();
                    $transaction_head = new LicenseTransaction();
                    $transaction_head->point_id = $point->id;
                    $transaction_head->plan_quantity = $request->input('total_point');
                    $transaction_head->created_by = $request->input('created_by');
                    $transaction_head->ip_address = $request->input('ip_address');
                    $transaction_head->save();
                    return $this->sendSuccess("New Point Added Successfully");
                }
            } else {
                return $this->sendSuccess("Requested Point Not Available");
            }
        }
        //Distributor to Dealer
        else  if ($admin_id != null && $distributor_id != null && $dealer_id != null && $subdealer_id == null) {

            $result = Point::where('total_point', '>=', $request->input('total_point'))
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
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
                    return $this->sendSuccess("New Point Added Successfully");
                } else {

                    $point = new Point($request->all());
                    $point->save();
                    $transaction_head = new LicenseTransaction();
                    $transaction_head->point_id = $point->id;
                    $transaction_head->plan_quantity = $request->input('total_point');
                    $transaction_head->created_by = $request->input('created_by');
                    $transaction_head->ip_address = $request->input('ip_address');
                    $transaction_head->save();
                    return $this->sendSuccess("New Point Added Successfully");
                }
            } else {
                return $this->sendSuccess("Requested Point Not Available");
            }
        }
        //Dealer to Sub Dealer
        else  if ($admin_id != null && $distributor_id != null && $dealer_id != null && $subdealer_id != null) {

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
                    return $this->sendSuccess("New Point Added Successfully");
                } else {

                    $point = new Point($request->all());
                    $point->save();
                    $transaction_head = new LicenseTransaction();
                    $transaction_head->point_id = $point->id;
                    $transaction_head->plan_quantity = $request->input('total_point');
                    $transaction_head->created_by = $request->input('created_by');
                    $transaction_head->ip_address = $request->input('ip_address');
                    $transaction_head->save();
                    return $this->sendSuccess("New Point Added Successfully");
                }
            } else {
                return $this->sendSuccess("Requested Point Not Available");
            }
        }
    }
}
