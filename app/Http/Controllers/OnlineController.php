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

            //Validation Code
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50',
                'email' => 'required|max:50|email|unique:users,email|unique:online_users,email',
                'mobile_no' => 'required|max:15|integer',
                'password' => 'required|max:8',
                'c_password' => 'required|max:8|same:password',
                'country_id' => 'required|integer',
                'app_id' => 'required|integer',
                'vehicle_type_id' => 'required|integer',
                'vehicle_name' => 'required|max:20',
                'barcode_no' => 'required|unique:online_vehicles,barcode_no',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $app_info = AppInfo::find($request->input('app_id'));
            if (!$app_info) {
                $response = ["success" => false, "message" => "App not valid", "status_code" => 404];
                return response()->json($response, 404);
            }
            $data['admin_id'] = $app_info->admin_id;
            $data['distributor_id'] = $app_info->distributor_id;
            $data['dealer_id'] = $app_info->dealer_id;
            $data['subdealer_id'] = $app_info->subdealer_id;

            $country = Country::find($request->input('country_id'));
            if (!$country) {
                $response = ["success" => false, "message" => "Country does not exist", "status_code" => 404];
                return response()->json($response, 404);
            }
            $data['country_id'] = $country->id;
            $data['country_name'] = $country->country_name;
            $data['timezone_name'] = $country->timezone_name;
            $data['timezone_offset'] = $country->timezone_offset;
            $data['timezone_minutes'] = $country->timezone_minutes;

            $stock = OnlineStock::where('barcode_no', $request->input('barcode_no'))
                ->where('admin_id',  $data['admin_id'])
                ->where('distributor_id',  $data['distributor_id'])
                ->where('dealer_id',  $data['dealer_id'])
                ->where('subdealer_id',  $data['subdealer_id'])
                ->where('status', 1)
                ->first();

            if (!$stock) {
                $response = ["success" => false, "message" => "Barcode is Invalid", "status_code" => 404];
                return response()->json($response, 404);
            } else {
                $data['name'] = $request->input('name');
                $data['email'] = $request->input('email');
                $data['mobile_no'] = $request->input('mobile_no');
                $data['password'] = $request->input('password');
                $data['country_id'] = $request->input('country_id');
                $data['address'] = $request->input('address');
                $data['app_id'] = $request->input('app_id');
                $data['app_name'] = $request->input('app_name');
                $data['ip_address'] = $request->ip();

                $result = OnlineUser::create($data);

                $vehicle['online_user_id'] = $result->id;
                $vehicle['barcode_no'] = $request->input('barcode_no');
                $vehicle['vehicle_type_id'] = $request->input('vehicle_type_id');
                $vehicle['vehicle_name'] = $request->input('vehicle_name');
                $vehicle['description'] = $request->input('description');
                $vehicle['app_id'] = $request->input('app_id');
                $vehicle['app_name'] = $request->input('app_name');
                $vehicle['admin_id'] = $app_info->admin_id;
                $vehicle['distributor_id'] = $app_info->distributor_id;
                $vehicle['dealer_id'] = $app_info->dealer_id;
                $vehicle['subdealer_id'] = $app_info->subdealer_id;
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
                'app_id' => 'required|integer',
                'vehicle_type_id' => 'required|integer',
                'vehicle_name' => 'required|max:20',
                'barcode_no' => 'required|unique:online_vehicles,barcode_no',
                'email' => 'required|max:50',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $app_info = AppInfo::find($request->input('app_id'));
            if (!$app_info) {
                $response = ["success" => false, "message" => "App not valid", "status_code" => 404];
                return response()->json($response, 404);
            }

            $stock = OnlineStock::where('barcode_no', $request->input('barcode_no'))
                ->where('admin_id',  $app_info->admin_id)
                ->where('distributor_id',  $app_info->distributor_id)
                ->where('dealer_id', $app_info->dealer_id)
                ->where('subdealer_id',  $app_info->subdealer_id)
                ->first();
            if (!$stock) {
                $response = ["success" => false, "message" => "Barcode is Invalid", "status_code" => 404];
                return response()->json($response, 404);
            }

            $user = OnlineUser::where('email', $request->input('email'))->first();
            if (!$user) {
                $response = ["success" => false, "message" => "User not valid", "status_code" => 404];
                return response()->json($response, 404);
            }

            $vehicle['online_user_id'] = $user->id;
            $vehicle['barcode_no'] = $request->input('barcode_no');
            $vehicle['vehicle_type_id'] = $request->input('vehicle_type_id');
            $vehicle['vehicle_name'] = $request->input('vehicle_name');
            $vehicle['description'] = $request->input('description');
            $vehicle['app_id'] = $request->input('app_id');
            $vehicle['app_name'] = $request->input('app_name');
            $vehicle['admin_id'] = $app_info->admin_id;
            $vehicle['distributor_id'] = $app_info->distributor_id;
            $vehicle['dealer_id'] = $app_info->dealer_id;
            $vehicle['subdealer_id'] = $app_info->subdealer_id;
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
}
