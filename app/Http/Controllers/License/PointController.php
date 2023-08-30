<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\License;
use Illuminate\Support\Facades\Validator;

use App\Models\Point;
use App\Models\LicenseTransaction;
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
        $subdealer_id = $request->input('dealer_id');

        $plan_id = $request->input('plan_id');
        $point_type_id = $request->input('point_type_id');



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


                // return response()->json($admin_id);


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
                            $prefix = "SWTLIC";
                            $date = Carbon::now()->format('Y');
                            $license_no = $prefix . $date . $maxId;
                            License::create(['license_no' => $license_no, 'plan_id' => $plan_id, 'admin_id' => $admin_id]);
                        }
                    }
                    return $this->sendSuccess("New Point Added Successfully");
                } else {

                    $point = new Point($request->all());
                    $point['admin_id'] = $admin_id;

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
                            License::create(['license_no' => $license_no, 'plan_id' => $plan_id, 'admin_id' => $admin_id]);
                        }
                    }
                    return $this->sendSuccess("New Point Added Successfully");
                }

                break;
            case $role_id == 2:
                $distributor_id = $request->input('distributor_id');
                $$result = Point::where('total_point', '>=', $request->input('total_point'))
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
                                ->update(['distributor_id' => $distributor_id]);
                        }

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

                        if ($point_type_id == 1) {
                            License::where('admin_id', $admin_id)
                                ->where('distributor_id', null)
                                ->where('dealer_id', null)
                                ->where('subdealer_id', null)
                                ->where('plan_id', $plan_id)
                                ->where('vehicle_id', null)
                                ->orderBy('id', 'asc')
                                ->limit($request->input('total_point'))
                                ->update(['distributor_id' => $distributor_id]);
                        }
                        return $this->sendSuccess("New Point Added Successfully");
                    }
                } else {
                    return $this->sendSuccess("Requested Point Not Availables");
                }
                break;
            case $role_id == 3:

                break;
            case $role_id == 4:

                break;
            default:
        }





        // if ($admin_id != null && $distributor_id != null && $dealer_id == null && $subdealer_id == null) {


        // }
        // //Distributor to Dealer
        // else  if ($admin_id != null && $distributor_id != null && $dealer_id != null && $subdealer_id == null) {

        //     $result = Point::where('total_point', '>=', $request->input('total_point'))
        //         ->where('admin_id', $admin_id)
        //         ->where('distributor_id', $distributor_id)
        //         ->where('dealer_id', null)
        //         ->where('subdealer_id', null)
        //         ->where('plan_id', $plan_id)
        //         ->where('point_type_id', $point_type_id)
        //         ->where('status', 1)
        //         ->first();

        //     // return response()->json($result);

        //     if (!empty($result)) {
        //         $result->total_point = $result->total_point - $request->input('total_point');
        //         $result->save();

        //         $result = Point::where('admin_id', $admin_id)
        //             ->where('distributor_id', $distributor_id)
        //             ->where('dealer_id', $dealer_id)
        //             ->where('subdealer_id', null)
        //             ->where('plan_id', $plan_id)
        //             ->where('point_type_id', $point_type_id)
        //             ->where('status', 1)
        //             ->first();
        //         if (!empty($result)) {
        //             $result->total_point = $result->total_point + $request->input('total_point');
        //             $result->save();
        //             $transaction_head = new LicenseTransaction();
        //             $transaction_head->point_id = $result->id;
        //             $transaction_head->plan_quantity = $request->input('total_point');
        //             $transaction_head->created_by = $request->input('created_by');
        //             $transaction_head->ip_address = $request->input('ip_address');
        //             $transaction_head->save();

        //             if ($point_type_id == 1) {
        //                 License::where('admin_id', $admin_id)
        //                     ->where('distributor_id', $distributor_id)
        //                     ->where('dealer_id', null)
        //                     ->where('subdealer_id', null)
        //                     ->where('plan_id', $plan_id)
        //                     ->where('vehicle_id', null)
        //                     ->orderBy('id', 'asc')
        //                     ->limit($request->input('total_point'))
        //                     ->update(['dealer_id' => $dealer_id]);
        //             }


        //             return $this->sendSuccess("New Point Added Successfully");
        //         } else {

        //             $point = new Point($request->all());
        //             $point->save();
        //             $transaction_head = new LicenseTransaction();
        //             $transaction_head->point_id = $point->id;
        //             $transaction_head->plan_quantity = $request->input('total_point');
        //             $transaction_head->created_by = $request->input('created_by');
        //             $transaction_head->ip_address = $request->input('ip_address');
        //             $transaction_head->save();

        //             if ($point_type_id == 1) {
        //                 License::where('admin_id', $admin_id)
        //                     ->where('distributor_id', $distributor_id)
        //                     ->where('dealer_id', null)
        //                     ->where('subdealer_id', null)
        //                     ->where('plan_id', $plan_id)
        //                     ->where('vehicle_id', null)
        //                     ->orderBy('id', 'asc')
        //                     ->limit($request->input('total_point'))
        //                     ->update(['dealer_id' => $dealer_id]);
        //             }

        //             return $this->sendSuccess("New Point Added Successfully");
        //         }
        //     } else {
        //         return $this->sendSuccess("Requested Point Not Availabless");
        //     }
        // }
        // //Dealer to Sub Dealer
        // else  if ($admin_id != null && $distributor_id != null && $dealer_id != null && $subdealer_id != null) {

        //     $result = Point::where('total_point', '>=', $request->input('total_point'))
        //         ->where('admin_id', $admin_id)
        //         ->where('distributor_id', $distributor_id)
        //         ->where('dealer_id', $dealer_id)
        //         ->where('subdealer_id', null)
        //         ->where('plan_id', $plan_id)
        //         ->where('point_type_id', $point_type_id)
        //         ->where('status', 1)
        //         ->first();

        //     return response()->json($result);

        //     if (!empty($result)) {
        //         $result->total_point = $result->total_point - $request->input('total_point');
        //         $result->save();

        //         $result = Point::where('admin_id', $admin_id)
        //             ->where('distributor_id', $distributor_id)
        //             ->where('dealer_id', $dealer_id)
        //             ->where('subdealer_id', $subdealer_id)
        //             ->where('plan_id', $plan_id)
        //             ->where('point_type_id', $point_type_id)
        //             ->where('status', 1)
        //             ->first();
        //         if (!empty($result)) {
        //             $result->total_point = $result->total_point + $request->input('total_point');
        //             $result->save();
        //             $transaction_head = new LicenseTransaction();
        //             $transaction_head->point_id = $result->id;
        //             $transaction_head->plan_quantity = $request->input('total_point');
        //             $transaction_head->created_by = $request->input('created_by');
        //             $transaction_head->ip_address = $request->input('ip_address');
        //             $transaction_head->save();


        //             if ($point_type_id == 1) {
        //                 License::where('admin_id', $admin_id)
        //                     ->where('distributor_id', $distributor_id)
        //                     ->where('dealer_id', $dealer_id)
        //                     ->where('subdealer_id', null)
        //                     ->where('plan_id', $plan_id)
        //                     ->where('vehicle_id', null)
        //                     ->orderBy('id', 'asc')
        //                     ->limit($request->input('total_point'))
        //                     ->update(['subdealer_id' => $subdealer_id]);
        //             }



        //             return $this->sendSuccess("New Point Added Successfully");
        //         } else {

        //             $point = new Point($request->all());
        //             $point->save();
        //             $transaction_head = new LicenseTransaction();
        //             $transaction_head->point_id = $point->id;
        //             $transaction_head->plan_quantity = $request->input('total_point');
        //             $transaction_head->created_by = $request->input('created_by');
        //             $transaction_head->ip_address = $request->input('ip_address');
        //             $transaction_head->save();

        //             if ($point_type_id == 1) {
        //                 License::where('admin_id', $admin_id)
        //                     ->where('distributor_id', $distributor_id)
        //                     ->where('dealer_id', $dealer_id)
        //                     ->where('subdealer_id', null)
        //                     ->where('plan_id', $plan_id)
        //                     ->where('vehicle_id', null)
        //                     ->orderBy('id', 'asc')
        //                     ->limit($request->input('total_point'))
        //                     ->update(['subdealer_id' => $subdealer_id]);
        //             }

        //             return $this->sendSuccess("New Point Added Successfully");
        //         }
        //     } else {
        //         return $this->sendSuccess("Requested Point Not Availablesss");
        //     }
        // }
    }
}
