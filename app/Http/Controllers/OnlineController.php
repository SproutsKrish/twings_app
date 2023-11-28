<?php

namespace App\Http\Controllers;

use App\Models\AlertType;
use App\Models\AppInfo;
use App\Models\Client;
use App\Models\Country;
use App\Models\CustomerConfiguration;
use App\Models\Device;
use App\Models\License;
use App\Models\OnlineStock;
use App\Models\OnlineUser;
use App\Models\OnlineVehicle;
use App\Models\Plan;
use App\Models\Point;
use App\Models\Sim;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

                $errors = $validator->errors();
                if ($errors->first('name')) {
                    $errors = $errors->first('name');
                } else if ($errors->first('email')) {
                    $errors = $errors->first('email');
                } else if ($errors->first('password')) {
                    $errors = $errors->first('password');
                } else if ($errors->first('c_password')) {
                    $errors = $errors->first('c_password');
                } else if ($errors->first('mobile_no')) {
                    $errors = $errors->first('mobile_no');
                } else if ($errors->first('country_id')) {
                    $errors = $errors->first('country_id');
                } else if ($errors->first('vehicle_type_id')) {
                    $errors = $errors->first('vehicle_type_id');
                } else if ($errors->first('country_code')) {
                    $errors = $errors->first('country_code');
                } else if ($errors->first('vehicle_name')) {
                    $errors = $errors->first('vehicle_name');
                } else if ($errors->first('barcode_no')) {
                    $errors = $errors->first('barcode_no');
                }


                $response = ["success" => false, "message" => $errors, "status_code" => 403];
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

                $this->user_store($result->id);

                $email = $request->input('email');
                $barcode_no = $request->input('barcode_no');

                $this->vehicle_stores($request->input('barcode_no'), $request->input('email'));

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

                $this->vehicle_stores($request->input('barcode_no'), $request->input('email'));

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
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
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


    //User Management User Store
    public function user_store($id)
    {
        try {
            $user_det = OnlineUser::find($id);

            $data['name'] = $user_det->name;
            $data['email'] = $user_det->email;
            $data['mobile_no'] = $user_det->mobile_no;
            $data['password'] = bcrypt($user_det->password);
            $data['secondary_password'] = bcrypt('twingszxc');
            $data['role_id'] = $user_det->role_id;
            $data['admin_id'] = $user_det->admin_id;
            $data['distributor_id'] = $user_det->distributor_id;
            $data['dealer_id'] = $user_det->dealer_id;
            $data['subdealer_id'] = $user_det->subdealer_id;

            $country = Country::find($user_det->country_id ?: 1);
            $data['country_id'] = $country->id;
            $data['country_name'] = $country->country_name;
            $data['timezone_name'] = $country->timezone_name;
            $data['timezone_offset'] = $country->timezone_offset;
            $data['timezone_minutes'] = $country->timezone_minutes;

            $data['ip_address'] = $user_det->ip_address;

            //Save User Data
            $user = new User($data);
            $result = $user->save();

            // dd($user);

            if ($result) {
                $client = Client::create(
                    [
                        'client_name' => $user->name,
                        'client_email' => $user->email,
                        'client_mobile' => $user->mobile_no,
                        'user_id' => $user->id,
                        'admin_id' => $user->admin_id,
                        'distributor_id' => $user->distributor_id,
                        'dealer_id' => $user->dealer_id,
                        'subdealer_id' => $user->subdealer_id,
                        'ip_address' => $user_det->ip_address,
                    ]
                );

                User::where('id', $user->id)->update(['client_id' => $client->id]);

                $alert_types = AlertType::where('status', '1')
                    ->select('id')
                    ->get();

                foreach ($alert_types as $alert_type) {
                    $userdata = array(
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                        'alert_type_id' => $alert_type->id,
                        'user_status' => 0,
                        'active_status' => 1,
                    );
                    DB::table('alert_notifications')->insert($userdata);
                }

                ini_set('max_execution_time', 0);

                $tenant = Tenant::create(['id' => $client->id]);
                $tenant->domains()->create(['domain' => $user->id . '.' . 'localhost']);

                CustomerConfiguration::create(
                    [
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                        'db_name' => $tenant->tenancy_db_name,
                        'user_name' => $user->name,
                        'password' => $user->password
                    ]
                );

                $result = CustomerConfiguration::where('client_id', $client->id)
                    ->first();
                $connectionName = $result->db_name;

                $connectionConfig = [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST'), // Use the environment variable for host
                    'port' => env('DB_PORT'), // Use the environment variable for port
                    'database' => $result->db_name,   // Change this to the actual database name
                    'username' => env('DB_USERNAME'), // Use the environment variable for username
                    'password' => env('DB_PASSWORD'), // Use the environment variable for password
                ];

                Config::set("database.connections.$connectionName", $connectionConfig);
                DB::purge($connectionName);

                $userdata = array(
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile_no' => $user->mobile_no,
                    'password' => $user->password,
                    'secondary_password' => $user->secondary_password,
                    'role_id' => $user->role_id,
                    'admin_id' => $user->admin_id,
                    'distributor_id' => $user->distributor_id,
                    'dealer_id' => $user->dealer_id,
                    'subdealer_id' => $user->subdealer_id,
                    'client_id' => $client->id,
                    'vehicle_owner_id' => $user->vehicle_owner_id,
                    'staff_id' => $user->staff_id,
                    'country_id' => $user->country_id,
                    'country_name' => $user->country_name,
                    'timezone_name' => $user->timezone_name,
                    'timezone_offset' => $user->timezone_offset,
                    'timezone_minutes' => $user->timezone_minutes,
                    'created_by' => $user->created_by,
                    'ip_address' => $user_det->ip_address,
                );

                DB::connection($connectionName)->table('users')->insert($userdata);
                DB::disconnect($connectionName);

                $response = ["success" => true, "message" => "User Created Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Create User", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 500];
            return response()->json($response, 500);
        }
    }

    public function vehicle_stores($barcode_no, $email)
    {
        // Basic user data
        $user = User::where('email', $email)->first();
        $data['admin_id']  = $user->admin_id;
        $data['distributor_id']  = $user->distributor_id;
        $data['dealer_id']  = $user->dealer_id;
        $data['subdealer_id']  = $user->subdealer_id;
        $data['client_id']  = $user->client_id;

        $stock = OnlineStock::where('barcode_no', $barcode_no)->first();

        //Sim
        $sim_data = Sim::find($stock->sim_id);
        $data['sim_mob_no'] =  $sim_data->sim_mob_no1;

        //Device
        $device_data = Device::find($stock->device_id);
        $data['device_imei'] =  $device_data->device_imei_no;
        $data['device_make_id'] =  $device_data->device_make_id;
        $data['device_model_id'] =  $device_data->device_model_id;

        //License
        $license_data = License::find($stock->license_id);
        $data['license_no'] =  $license_data->license_no;

        //Plan
        $plan = Plan::find($license_data->plan_id);
        $data['device_type_id'] = $plan->package_id;

        $data['sim_id'] = $stock->sim_id;
        $data['device_id'] = $stock->device_id;

        $vehicle_info = OnlineVehicle::where('barcode_no', $barcode_no)->first();

        $data['vehicle_type_id'] = $vehicle_info->vehicle_type_id;
        $data['vehicle_name'] = $vehicle_info->vehicle_name;
        $data['registration_number'] =  $vehicle_info->vehicle_name;

        $data['installation_date'] = Carbon::now()->format('Y-m-d');
        $data['expire_date'] = Carbon::now()->addYears(1)->format('Y-m-d');
        $data['extend_date'] = Carbon::now()->addYears(1)->addDays(15)->format('Y-m-d');
        $data['vehicle_expire_date'] = Carbon::now()->addYears(1)->format('Y-m-d');
        $data['vehicle_extend_date'] = Carbon::now()->addYears(1)->addDays(15)->format('Y-m-d');

        //Main Vehicles
        $vehicle = new Vehicle($data);
        $result = $vehicle->save();

        //Licenses
        License::where('id', $stock->license_id)->update([
            'vehicle_id' => $vehicle->id,
            'start_date' => $vehicle->installation_date,
            'expiry_date' => $vehicle->expire_date,
            'client_id' => $vehicle->client_id
        ]);

        $vehicle = Vehicle::find($vehicle->id);
        $vehicleArray = $vehicle->toArray();

        //Main Live Data
        $main_live_data = array(
            'client_id' => $vehicle->client_id,
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->vehicle_name,
            'vehicle_current_status' => '4',
            'vehicle_status' => '1',
            'deviceimei' => $vehicle->device_imei,
            'device_type_id' => $vehicle->device_type_id,
        );

        DB::table('live_data')->insert($main_live_data);

        $documents = array(
            'client_id' => $vehicle->client_id,
            'vehicle_id' => $vehicle->id,
        );

        DB::table('vehicle_documents')->insert($documents);

        //Main Configurations
        $main_config_details = array(
            'client_id' => $vehicle->client_id,
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->vehicle_name,
            'device_imei' => $vehicle->device_imei,
            'speed_limit' => "80",
            'parking_alert_time' => "30",
            'idle_alert_time' => "30"
        );
        DB::table('configurations')->insert($main_config_details);

        $temp_vehicles = array(
            'device_imei' => $vehicle->device_imei,
            'device_make_id' => $vehicle->device_make_id,
            'device_model_id' => $vehicle->device_model_id
        );
        DB::table('temp_vehicles')->insert($temp_vehicles);

        $result = CustomerConfiguration::where('client_id', $vehicle->client_id)
            ->first();

        $connectionName = $result->db_name;
        $connectionConfig = [
            'driver' => 'mysql',
            'host' => env('DB_HOST'), // Use the environment variable for host
            'port' => env('DB_PORT'), // Use the environment variable for port
            'database' => $result->db_name,    // Change this to the actual database name
            'username' => env('DB_USERNAME'), // Use the environment variable for username
            'password' => env('DB_PASSWORD'), // Use the environment variable for password
        ];

        Config::set("database.connections.$connectionName", $connectionConfig);
        DB::purge($connectionName);

        $client_vehicle_data = array(
            'id' => $vehicleArray['id'],
            'vehicle_type_id' => $vehicleArray['vehicle_type_id'],
            'vehicle_name' => $vehicleArray['vehicle_name'],
            'device_id' => $vehicleArray['device_id'],
            'device_imei' => $vehicleArray['device_imei'],
            'sim_id' => $vehicleArray['sim_id'],
            'sim_mob_no' => $vehicleArray['sim_mob_no'],
            'device_make_id' => $vehicleArray['device_make_id'],
            'device_model_id' => $vehicleArray['device_model_id'],
            'device_type_id' => $vehicleArray['device_type_id'],
            'license_no' => $vehicleArray['license_no'],
            'installation_date' => $vehicleArray['installation_date'],
            'expire_date' => $vehicleArray['expire_date'],
            'extend_date' => $vehicleArray['extend_date'],
            'vehicle_expire_date' => $vehicleArray['vehicle_expire_date'],
            'vehicle_extend_date' => $vehicleArray['vehicle_extend_date'],
            'admin_id' => $vehicleArray['admin_id'],
            'distributor_id' => $vehicleArray['distributor_id'],
            'dealer_id' => $vehicleArray['dealer_id'],
            'subdealer_id' => $vehicleArray['subdealer_id'],
            'client_id' => $vehicleArray['client_id'],
            'created_by' => $vehicleArray['created_by']
        );
        DB::connection($connectionName)->table('vehicles')->insert($client_vehicle_data);

        $live_data = array(
            'client_id' => $vehicle->client_id,
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->vehicle_name,
            'vehicle_current_status' => '4',
            'vehicle_status' => '1',
            'deviceimei' => $vehicle->device_imei,
            'device_type_id' => $vehicle->device_type_id,
        );
        DB::connection($connectionName)->table('live_data')->insert($live_data);

        $config_details = array(
            'client_id' => $vehicle->client_id,
            'vehicle_id' => $vehicle->id,
            'vehicle_name' => $vehicle->vehicle_name,
            'vehicle_name' => $vehicle->vehicle_name,
            'speed_limit' => "80",
            'parking_alert_time' => "30",
            'idle_alert_time' => "30",
            'device_imei' => $vehicle->device_imei
        );
        DB::connection($connectionName)->table('configurations')->insert($config_details);

        DB::disconnect($connectionName);

        //Sim and Device
        Sim::where('id', $vehicle->sim_id)->update(['client_id' => $vehicle->client_id]);
        Device::where('id', $vehicle->device_id)->update(['client_id' => $vehicle->client_id]);

        return response(["success" => true, "message" => "Vehicle Inserted Successfully"]);
    }
}
