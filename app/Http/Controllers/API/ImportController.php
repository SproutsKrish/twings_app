<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use App\Models\Sim;
use App\Models\Camera;
use App\Models\Client;
use App\Models\CustomerConfiguration;
use App\Models\Dealer;
use App\Models\Device;
use App\Models\Distributor;
use App\Models\License;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Point;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vehicle;

class ImportController extends BaseController
{

    public function sim_import(Request $request)
    {
        $file_path = $request->input('file_path');
        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required', // network_id
                    1 => 'required|unique:sims,sim_imei_no', // sim_imei_no (unique in 'sims' table)
                    2 => 'required|unique:sims,sim_mob_no', // sim_mob_no (unique in 'sims' table)
                    3 => 'required', // valid_from
                    4 => 'required', // valid_to
                    5 => 'required', // purchase_date
                    6 => 'required' // created_by
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                Sim::create([
                    'network_id' => $row[0],
                    'sim_imei_no' => $row[1],
                    'sim_mob_no' => $row[2],
                    'valid_from' => $row[3],
                    'valid_to' => $row[4],
                    'purchase_date' => $row[5],
                    'created_by' => $row[6]
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Sim Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }

    public function device_import(Request $request)
    {
        $file_path = $request->input('file_path');
        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required', // supplier_id
                    1 => 'required', // device_type_id
                    2 => 'required', // device_category_id
                    3 => 'required', // device_model_id
                    4 => 'required|unique:devices,device_imei_no', // device_imei_no (unique in 'devices' table)
                    10 => 'required', // purchase_date
                    11 => 'required' // created_by
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                Device::create([
                    'supplier_id' => $row[0],
                    'device_type_id' => $row[1],
                    'device_category_id' => $row[2],
                    'device_model_id' => $row[3],
                    'device_imei_no' => $row[4],
                    'ccid' => $row[5],
                    'uid' => $row[6],
                    'start_date' => $row[7],
                    'end_date' => $row[8],
                    'sensor_name' => $row[9],
                    'purchase_date' => $row[10],
                    'created_by' => $row[11]
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Device Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }

    public function camera_import(Request $request)
    {
        $file_path = $request->input('file_path');
        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required', // supplier_id
                    1 => 'required', // camera_type_id
                    2 => 'required', // camera_category_id
                    3 => 'required', // camera_model_id
                    4 => 'required|unique:cameras,serial_no', // device_imei_no (unique in 'devices' table)
                    6 => 'required', // purchase_date
                    7 => 'required' // created_by
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                Camera::create([
                    'supplier_id' => $row[0],
                    'camera_type_id' => $row[1],
                    'camera_category_id' => $row[2],
                    'camera_model_id' => $row[3],
                    'serial_no' => $row[4],
                    'id_no' => $row[5],
                    'purchase_date' => $row[6],
                    'created_by' => $row[7]
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Camera Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }


    // public function user_import(Request $request)
    // {
    //     $file_path = $request->input('file_path');

    //     if (!$file_path) {
    //         return $this->sendError("No File Path Provided");
    //     }

    //     $validator = Validator::make($request->all(), ['file_path' => 'required']);

    //     if ($validator->fails()) {
    //         return $this->sendError("Invalid File Format");
    //     }

    //     try {
    //         $path = $file_path;
    //         $data = array_map('str_getcsv', file($path));

    //         DB::beginTransaction();

    //         foreach ($data as $row) {
    //             $rowValidator = Validator::make($row, [
    //                 0 => 'required', // name (unique in 'users' table)
    //                 1 => 'required|unique:users,email', // email (unique in 'users' table)
    //                 2 => 'required', // password
    //                 3 => 'required', // secondary_password
    //                 4 => 'required', // role_id
    //             ]);

    //             if ($rowValidator->fails()) {
    //                 DB::rollBack();
    //                 return $this->sendError($rowValidator->errors());
    //             }

    //             $user =  User::create([
    //                 'name' => $row[0],
    //                 'email' => $row[1],
    //                 'password' => bcrypt($row[2]),
    //                 'secondary_password' => bcrypt($row[3]),
    //                 'mobile_no' => $row[4],
    //                 'role_id' => $row[5],
    //                 'admin_id' => $row[6],
    //                 'distributor_id' => $row[7],
    //                 'dealer_id' => $row[8],
    //                 'created_by' => auth()->user()->id,
    //                 'ip_address' => $request->ip(),
    //             ]);

    //             $client = Client::create(
    //                 [
    //                     'client_company' => $user->name,
    //                     'client_name' => $user->name,
    //                     'client_email' => $user->email,
    //                     'client_mobile' => $user->mobile_no,
    //                     'user_id' => $user->id,
    //                     'admin_id' => $user->admin_id,
    //                     'distributor_id' => $user->distributor_id,
    //                     'dealer_id' => $user->dealer_id,
    //                     'created_by' => auth()->user()->id,
    //                     'ip_address' => $request->ip(),
    //                 ]
    //             );

    //             User::where('id', $user->id)
    //                 ->update([
    //                     'client_id' => $client->id
    //                 ]);


    //             $alert_types =  DB::table('alert_types')
    //                 ->where('status', '1')
    //                 ->select('id')
    //                 ->get();

    //             foreach ($alert_types as $alert_type) {
    //                 $userdata = array(
    //                     'user_id' => $user->id,
    //                     'client_id' => $client->id,
    //                     'alert_type_id' => $alert_type->id,
    //                     'user_status' => 0,
    //                     'active_status' => 1,
    //                 );
    //                 DB::table('alert_notifications')->insert($userdata);
    //             }

    //             $tenant = Tenant::create(['id' => $client->id]);

    //             $customer_configurations = CustomerConfiguration::create(
    //                 [
    //                     'user_id' => $user->id,
    //                     'client_id' => $client->id,
    //                     'db_name' => $tenant->tenancy_db_name,
    //                     'user_name' => $user->name,
    //                     'password' => $user->password
    //                 ]
    //             );
    //             $tenant->domains()->create(['domain' => $user->name . '.' . 'localhost']);


    //             $result = CustomerConfiguration::where('client_id', $client->id)
    //                 ->first();

    //             $connectionName = $result->db_name;
    //             $connectionConfig = [
    //                 'driver' => 'mysql',
    //                 'host' => env('DB_HOST'), // Use the environment variable for host
    //                 'port' => env('DB_PORT'), // Use the environment variable for port
    //                 'database' => $result->db_name,   // Change this to the actual database name
    //                 'username' => env('DB_USERNAME'), // Use the environment variable for username
    //                 'password' => env('DB_PASSWORD'), // Use the environment variable for password
    //             ];

    //             Config::set("database.connections.$connectionName", $connectionConfig);
    //             DB::purge($connectionName);

    //             $userdata = array(
    //                 'id' => $user->id,
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //                 'mobile_no' => $user->mobile_no,
    //                 'password' => $user->password,
    //                 'secondary_password' => $user->secondary_password,
    //                 'role_id' => $user->role_id,
    //                 'admin_id' => $user->admin_id,
    //                 'distributor_id' => $user->distributor_id,
    //                 'dealer_id' => $user->dealer_id,
    //                 'subdealer_id' => $user->subdealer_id,
    //                 'client_id' => $client->id,
    //                 'vehicle_owner_id' => $user->vehicle_owner_id,
    //                 'staff_id' => $user->staff_id,
    //                 'country_id' => $user->country_id,
    //                 'country_name' => $user->country_name,
    //                 'timezone_name' => $user->timezone_name,
    //                 'timezone_offset' => $user->timezone_offset,
    //                 'timezone_minutes' => $user->timezone_minutes,
    //                 'created_by' => $user->created_by,
    //                 'ip_address' => $request->ip()
    //             );
    //             DB::connection($connectionName)->table('users')->insert($userdata);
    //         }

    //         DB::commit();

    //         return $this->sendSuccess('User Imported Successfully');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
    //     }
    // }


    public function user_import(Request $request)
    {
        try {
            ini_set('max_execution_time', 0);

            DB::beginTransaction();
            $data = DB::table('twings.user')->whereBetween('id', [923, 1000])->select('name', 'email', 'password', 'sec_pass', 'mobile_no', 'role_id', 'admin_id', 'distributor_id', 'dealer_id')->get();
            foreach ($data as $row) {
                $user =  User::create([
                    'name' => $row->name,
                    'email' => $row->email,
                    'password' => bcrypt($row->password),
                    'secondary_password' => bcrypt($row->sec_pass),
                    'mobile_no' => $row->mobile_no,
                    'role_id' => $row->role_id,
                    'admin_id' => $row->admin_id,
                    'distributor_id' => $row->distributor_id,
                    'dealer_id' => $row->dealer_id,
                    'created_by' => auth()->user()->id,
                    'ip_address' => $request->ip(),
                ]);

                $client = Client::create(
                    [
                        'client_company' => $user->name,
                        'client_name' => $user->name,
                        'client_email' => $user->email,
                        'client_mobile' => $user->mobile_no,
                        'user_id' => $user->id,
                        'admin_id' => $user->admin_id,
                        'distributor_id' => $user->distributor_id,
                        'dealer_id' => $user->dealer_id,
                        'created_by' => auth()->user()->id,
                        'ip_address' => $request->ip(),
                    ]
                );

                User::where('id', $user->id)
                    ->update([
                        'client_id' => $client->id
                    ]);


                $alert_types =  DB::table('alert_types')
                    ->where('status', '1')
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

                $tenant = Tenant::create(['id' => $client->id]);

                $customer_configurations = CustomerConfiguration::create(
                    [
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                        'db_name' => $tenant->tenancy_db_name,
                        'user_name' => $user->name,
                        'password' => $user->password
                    ]
                );
                $tenant->domains()->create(['domain' => $user->id . '.' . 'localhost']);


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
                    'ip_address' => $request->ip()
                );
                DB::connection($connectionName)->table('users')->insert($userdata);
            }

            DB::commit();

            return $this->sendSuccess('User Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }


    public function admin_import(Request $request)
    {
        $file_path = $request->input('file_path');

        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required|unique:users,name', // name (unique in 'users' table)
                    1 => 'required|unique:users,email', // email (unique in 'users' table)
                    2 => 'required', // password
                    3 => 'required', // secondary_password
                    4 => 'required', // role_id
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                $user =  User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'password' => bcrypt($row[2]),
                    'secondary_password' => bcrypt($row[3]),
                    'mobile_no' => $row[4],
                    'role_id' => $row[5],
                    'created_by' => auth()->user()->id,
                    'ip_address' => $request->ip(),
                ]);

                $admin = Admin::create(
                    [
                        'admin_company' => $row[6],
                        'admin_name' => $user->name,
                        'admin_email' => $user->email,
                        'admin_mobile' => $user->mobile_no,
                        'user_id' => $user->id,
                        'created_by' => auth()->user()->id,
                        'ip_address' => $request->ip(),
                    ]
                );

                User::where('id', $user->id)
                    ->update(['admin_id' => $admin->id]);
            }

            DB::commit();

            return $this->sendSuccess('Admin Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }

    public function distributor_import(Request $request)
    {

        $file_path = $request->input('file_path');

        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required|unique:users,name', // name (unique in 'users' table)
                    1 => 'required|unique:users,email', // email (unique in 'users' table)
                    2 => 'required', // password
                    3 => 'required', // secondary_password
                    4 => 'required', // role_id
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                $user =  User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'password' => bcrypt($row[2]),
                    'secondary_password' => bcrypt($row[3]),
                    'role_id' => $row[4],
                    'admin_id' => $row[5],
                    'created_by' => auth()->user()->id,
                    'ip_address' => $request->ip(),
                ]);

                $distributor = Distributor::create(
                    [
                        'distributor_company' => $row[0],
                        'distributor_name' => $user->name,
                        'distributor_email' => $user->email,
                        'user_id' => $user->id,
                        'admin_id' => $user->admin_id,
                        'created_by' => auth()->user()->id,
                        'ip_address' => $request->ip(),
                    ]
                );

                User::where('id', $user->id)
                    ->update(['admin_id' => $user->admin_id, 'distributor_id' => $distributor->id]);
            }

            DB::commit();

            return $this->sendSuccess('Distributor Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }

    public function dealer_import(Request $request)
    {
        $file_path = $request->input('file_path');

        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required|unique:users,name', // name (unique in 'users' table)
                    1 => 'required|unique:users,email', // email (unique in 'users' table)
                    2 => 'required', // password
                    3 => 'required', // secondary_password
                    4 => 'required', // role_id
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                $user =  User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'password' => bcrypt($row[2]),
                    'secondary_password' => bcrypt($row[3]),
                    'role_id' => $row[4],
                    'admin_id' => $row[5],
                    'distributor_id' => $row[6],
                    'created_by' => auth()->user()->id,
                    'ip_address' => $request->ip(),
                ]);

                $dealer = Dealer::create(
                    [
                        'dealer_company' => $row[0],
                        'dealer_name' => $user->name,
                        'dealer_email' => $user->email,
                        'user_id' => $user->id,
                        'admin_id' => $user->admin_id,
                        'distributor_id' => $user->distributor_id,
                        'created_by' => auth()->user()->id,
                        'ip_address' => $request->ip(),
                    ]
                );

                User::where('id', $user->id)
                    ->update([
                        'admin_id' => $user->admin_id,
                        'distributor_id' => $user->distributor_id,
                        'dealer_id' => $dealer->id
                    ]);
            }

            DB::commit();

            return $this->sendSuccess('Dealer Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }


    public function vehicle_import(Request $request)
    {
        $file_path = $request->input('file_path');

        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();
            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required|unique:vehicles,sim_id',
                    1 => 'required|unique:vehicles,device_id',
                    2 => 'required', // vehicle_type_id
                    3 => 'required', // vehicle_name
                    4 => 'required', // installation_date
                    5 => 'required', // plan_id
                    6 => 'required', // license_id
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                $sim_data = Sim::find($row[0]);
                $sim_mob_no =  $sim_data->sim_mob_no1;

                $device_data = Device::find($row[1]);
                $device_imei =  $device_data->device_imei_no;
                $device_make_id =  $device_data->device_make_id;
                $device_model_id =  $device_data->device_model_id;

                $license_data = License::find($row[6]);
                $license_no =  $license_data->license_no;
                // dd($row[5]);

                if ($row[10] == "") {
                    $row[10] = null;
                }
                DB::enableQueryLog();
                $result = Point::where('total_point', '>', 0)
                    ->where('admin_id',  $row[7])
                    ->where('distributor_id', $row[8])
                    ->where('dealer_id', $row[9])
                    ->where('subdealer_id', $row[10])
                    ->where('plan_id', $row[5])
                    ->where('point_type_id', "1")
                    ->where('status', 1)
                    ->first();

                // dd(DB::getQueryLog());
                // dd($result);

                if (!empty($result)) {
                    //Points
                    $result->total_point = $result->total_point - 1;
                    $result->save();

                    $plan_id = $result->plan_id;
                    $plan = Plan::find($plan_id);
                    $period_id = $plan->period_id;
                    $period = Period::find($period_id);

                    $newstart_date = \Carbon\Carbon::createFromFormat('d-m-Y', $row[4]);
                    $newDateTime = $newstart_date->addDays($period->period_days);
                    $expire_date = $newDateTime->format('Y-m-d');
                    $installation_date = \Carbon\Carbon::createFromFormat('d-m-Y', $row[4])->format('Y-m-d');

                    $vehicle =  Vehicle::create([
                        'sim_id' => $row[0],
                        'device_id' => $row[1],
                        'vehicle_type_id' => $row[2],
                        'vehicle_name' => $row[3],
                        'installation_date' => $installation_date,
                        'admin_id' => $row[7],
                        'distributor_id' => $row[8],
                        'dealer_id' => $row[9],
                        'subdealer_id' => $row[10],
                        'client_id' => $row[11],
                        'sim_mob_no' => $sim_mob_no,
                        'device_make_id' => $device_make_id,
                        'device_model_id' => $device_model_id,
                        'device_imei' => $device_imei,
                        'license_no' => $license_no,
                        'expire_date' => $expire_date,
                        'created_by' => auth()->user()->id,
                        'ip_address' => $request->ip(),
                    ]);

                    //Licenses
                    License::where('id', $row[6])->update([
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
                        'deviceimei' => $vehicle->device_imei
                    );

                    DB::table('live_data')->insert($main_live_data);

                    //Main Configurations
                    $main_config_details = array(
                        'client_id' => $vehicle->client_id,
                        'vehicle_id' => $vehicle->id,
                        'vehicle_name' => $vehicle->vehicle_name,
                        'device_imei' => $vehicle->device_imei
                    );
                    DB::table('configurations')->insert($main_config_details);


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
                        'installation_date' => $vehicleArray['installation_date'],
                        'expire_date' => $vehicleArray['expire_date'],
                        'admin_id' => $vehicleArray['admin_id'],
                        'distributor_id' => $vehicleArray['distributor_id'],
                        'dealer_id' => $vehicleArray['dealer_id'],
                        'subdealer_id' => $vehicleArray['subdealer_id'],
                        'client_id' => $vehicleArray['client_id'],
                        'created_by' => $vehicleArray['created_by']
                    );
                    // Client Vehicles
                    DB::connection($connectionName)->table('vehicles')->insert($client_vehicle_data);
                    $live_data = array(
                        'client_id' => $vehicle->client_id,
                        'vehicle_id' => $vehicle->id,
                        'vehicle_name' => $vehicle->vehicle_name,
                        'vehicle_current_status' => '4',
                        'vehicle_status' => '1',
                        'deviceimei' => $vehicle->device_imei
                    );
                    // Client Live Data
                    DB::connection($connectionName)->table('live_data')->insert($live_data);

                    $config_details = array(
                        'client_id' => $vehicle->client_id,
                        'vehicle_id' => $vehicle->id,
                        'vehicle_name' => $vehicle->vehicle_name,
                        'device_imei' => $vehicle->device_imei
                    );

                    // Client Configurations
                    DB::connection($connectionName)->table('configurations')->insert($config_details);

                    DB::disconnect($connectionName);

                    //Sim and Device
                    Sim::where('id', $vehicle->sim_id)->update(['client_id' => $vehicle->client_id]);
                    Device::where('id', $vehicle->device_id)->update(['client_id' => $vehicle->client_id]);
                }
            }

            DB::commit();
            return $this->sendSuccess('Vehicle Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }
}
