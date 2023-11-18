<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use App\Models\Country;
use App\Models\OnlineStock;
use App\Models\OnlineUser;
use App\Models\OnlineVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnlineController extends Controller
{
    public function store(Request $request)
    {
        //Validation Code
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'email' => 'required|email|unique:online_users,email',
            'mobile_no' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'country_id' => 'required',
            'app_id' => 'required',
            'barcode_no' => 'required|unique:online_vehicles,barcode_no',
            'vehicle_type_id' => 'required',
            'vehicle_name' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $data['name'] = $request->input('name');
        $data['email'] = $request->input('email');
        $data['mobile_no'] = $request->input('mobile_no');
        $data['password'] = $request->input('password');
        $data['country_id'] = $request->input('country_id');
        $data['address'] = $request->input('address');
        $data['app_id'] = $request->input('app_id');
        $data['app_name'] = $request->input('app_name');

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

        $data['ip_address'] = $request->ip();

        $result = OnlineUser::create($data);

        $stock = OnlineStock::where($request->input('barcode_no'))->first();
        if (!$stock) {
            $response = ["success" => false, "message" => "Barcode is Invalid", "status_code" => 404];
            return response()->json($response, 404);
        }

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

        if ($results) {
            $response = ["success" => true, "message" => "Data Saved Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Data Not Saved", "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function vehicle_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_id' => 'required',
            'sim_imei_no' => 'required',
            'sim_mob_no1' => 'required',
            'device_imei' => 'required',
            'device_ccid' => 'required',
            'vehicle_type_id' => 'required',
            'vehicle_name' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        // $vehicle['online_user_id'] = $result->id;
        $vehicle['sim_imei_no'] = $request->input('sim_imei_no');
        $vehicle['sim_mob_no1'] = $request->input('sim_mob_no1');
        $vehicle['sim_mob_no2'] = $request->input('sim_mob_no2');
        $vehicle['device_imei'] = $request->input('device_imei');
        $vehicle['device_ccid'] = $request->input('device_ccid');
        $vehicle['device_uid'] = $request->input('device_uid');

        $vehicle['vehicle_type_id'] = $request->input('vehicle_type_id');
        $vehicle['vehicle_name'] = $request->input('vehicle_name');
        $vehicle['description'] = $request->input('description');

        $vehicle['app_id'] = $request->input('app_id');
        $vehicle['app_name'] = $request->input('app_name');

        $app_info = AppInfo::find($request->input('app_id'));
        if (!$app_info) {
            $response = ["success" => false, "message" => "App not valid", "status_code" => 404];
            return response()->json($response, 404);
        }
        $vehicle['admin_id'] = $app_info->admin_id;
        $vehicle['distributor_id'] = $app_info->distributor_id;
        $vehicle['dealer_id'] = $app_info->dealer_id;
        $vehicle['subdealer_id'] = $app_info->subdealer_id;
        $vehicle['ip_address'] = $request->ip();

        $results = OnlineVehicle::create($vehicle);

        $response = ["success" => false, "message" => "Data Saved Successfully", "status_code" => 200];
        return response()->json($response, 200);
    }
}
