<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use App\Models\Country;
use App\Models\OnlineStock;
use App\Models\OnlineUser;
use App\Models\OnlineVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OnlineController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50',
                'email' => 'required|max:50|email|unique:users,email|unique:online_users,email',
                'password' => 'required|max:8',
                'c_password' => 'required|max:8|same:password',
                'mobile_no' => 'required|integer',
                'country_id' => 'required|integer',
                'vehicle_type_id' => 'required|integer',
                'country_code' => 'required|max:8',
                'vehicle_name' => 'required|max:30',
                'barcode_no' => 'required|unique:online_vehicles,barcode_no',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $app_info = AppInfo::where('app_package_name', $request->input('app_package_name'))->first();
            if (!$app_info) {
                $response = ["success" => false, "message" => "App not valid", "status_code" => 403];
                return response()->json($response, 403);
            }
            $data['admin_id'] = $app_info->admin_id;
            $data['distributor_id'] = $app_info->distributor_id;
            $data['dealer_id'] = $app_info->dealer_id;
            $data['subdealer_id'] = $app_info->subdealer_id;
            $data['app_id'] = $app_info->id;
            $data['app_package_name'] = $app_info->app_package_name;

            $country = Country::find($request->input('country_id'));
            if (!$country) {
                $response = ["success" => false, "message" => "Country does not exist", "status_code" => 403];
                return response()->json($response, 403);
            }

            $data['name'] = $request->input('name');
            $data['email'] = $request->input('email');
            $data['mobile_no'] = $request->input('mobile_no');
            $data['password'] = $request->input('password');
            $data['country_id'] = $request->input('country_id');
            $data['country_code'] = $request->input('country_code');
            $data['address'] = $request->input('address');
            $data['ip_address'] = $request->ip();

            $result = OnlineUser::create($data);

            if ($result) {
                $stock = OnlineStock::where('barcode_no', $request->input('barcode_no'))
                    ->where('admin_id',  $app_info->admin_id)
                    ->where('distributor_id',  $app_info->distributor_id)
                    ->where('dealer_id', $app_info->dealer_id)
                    ->where('subdealer_id',  $app_info->subdealer_id)
                    ->where('status', 1)
                    ->first();
                if (!$stock) {
                    $response = ["success" => false, "message" => "Barcode is Invalid", "status_code" => 403];
                    return response()->json($response, 403);
                }

                $vehicle['online_user_id'] = $result->id;
                $vehicle['vehicle_type_id'] = $request->input('vehicle_type_id');
                $vehicle['vehicle_name'] = $request->input('vehicle_name');
                $vehicle['barcode_no'] = $request->input('barcode_no');
                $vehicle['app_id'] =  $result->app_id;
                $vehicle['app_package_name'] = $result->app_package_name;
                $vehicle['admin_id'] = $result->admin_id;
                $vehicle['distributor_id'] = $result->distributor_id;
                $vehicle['dealer_id'] = $result->dealer_id;
                $vehicle['subdealer_id'] = $result->subdealer_id;
                $vehicle['ip_address'] = $request->ip();

                OnlineVehicle::create($vehicle);

                $stock->status = 2;
                $stock->update();

                DB::commit();
                $response = ["success" => true, "message" => "Data Saved Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                DB::rollBack();
                $response = ["success" => false, "message" => "Data Not Saved", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 500];
            return response()->json($response, 500);
        }
    }

    public function vehicle_store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'vehicle_type_id' => 'required|integer',
                'vehicle_name' => 'required|max:20',
                'barcode_no' => 'required|unique:online_vehicles,barcode_no',
                'email' => 'required|max:20'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $user = OnlineUser::where('email', $request->input('email'))->first();
            if (!$user) {
                $response = ["success" => false, "message" => "User not valid", "status_code" => 403];
                return response()->json($response, 403);
            }

            $stock = OnlineStock::where('barcode_no', $request->input('barcode_no'))
                ->where('admin_id',  $user->admin_id)
                ->where('distributor_id',  $user->distributor_id)
                ->where('dealer_id', $user->dealer_id)
                ->where('subdealer_id',  $user->subdealer_id)
                ->where('status', 1)
                ->first();
            if (!$stock) {
                $response = ["success" => false, "message" => "Barcode is Invalid", "status_code" => 404];
                return response()->json($response, 404);
            }

            $vehicle['online_user_id'] = $user->id;
            $vehicle['vehicle_type_id'] = $request->input('vehicle_type_id');
            $vehicle['vehicle_name'] = $request->input('vehicle_name');
            $vehicle['barcode_no'] = $request->input('barcode_no');
            $vehicle['app_id'] =  $user->app_id;
            $vehicle['app_package_name'] = $user->app_package_name;
            $vehicle['admin_id'] = $user->admin_id;
            $vehicle['distributor_id'] = $user->distributor_id;
            $vehicle['dealer_id'] = $user->dealer_id;
            $vehicle['subdealer_id'] = $user->subdealer_id;
            $vehicle['ip_address'] = $request->ip();

            $results = OnlineVehicle::create($vehicle);

            $stock->status = 2;
            $stock->update();

            if ($results) {
                DB::commit();
                $response = ["success" => true, "message" => "Data Saved Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                DB::rollBack();
                $response = ["success" => false, "message" => "Data Not Saved", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 500];
            return response()->json($response, 500);
        }
    }

    public function validate_user_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:50|email|unique:users,email|unique:online_users,email',
            'password' => 'required|max:8',
            'c_password' => 'required|max:8|same:password',
            'barcode_no' => 'required|unique:online_vehicles,barcode_no',
        ]);

        if ($validator->fails()) {
            $response = ["success" => true, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        } else {
            $stock = OnlineStock::where('barcode_no', $request->input('barcode_no'))
                ->where('status', 1)
                ->first();
            if (!$stock) {
                $response = ["success" => false, "message" => "Barcode is Invalid", "status_code" => 403];
                return response()->json($response, 403);
            } else {
                $response = ["success" => true, "message" => "987123", "status_code" => 200];
                return response()->json($response, 200);
            }
        }
    }
}
