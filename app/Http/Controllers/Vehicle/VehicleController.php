<?php

namespace App\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\CustomerConfiguration;
use App\Models\Device;
use App\Models\License;
use App\Models\Period;
use App\Models\Plan;
use App\Models\Point;
use App\Models\Sim;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleController extends BaseController
{
    public function index()
    {
        $vehicles = Vehicle::all();

        if ($vehicles->isEmpty()) {
            return $this->sendError('No Vehicles Found');
        }

        return $this->sendSuccess($vehicles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sim_id' => 'required',
            'device_id' => 'required|unique:vehicles,device_id',
            'vehicle_type_id' => 'required',
            'vehicle_name' => 'required',
            'plan_id' => 'required',
            'license_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $requestKeys = collect($request->all())->keys();

        $sim_data = Sim::find($request->input('sim_id'));
        $data['sim_mob_no'] =  $sim_data->sim_mob_no1;
        $device_data = Device::find($request->input('device_id'));
        $data['device_imei'] =  $device_data->device_imei_no;
        $data['device_make_id'] =  $device_data->device_make_id;
        $data['device_model_id'] =  $device_data->device_model_id;
        $license_data = License::find($request->input('license_id'));
        $data['license_no'] =  $license_data->license_no;

        $data['admin_id'] = auth()->user()->admin_id;
        $data['distributor_id'] = auth()->user()->distributor_id;
        $data['dealer_id'] = auth()->user()->dealer_id;
        $data['subdealer_id'] = auth()->user()->subdealer_id;

        if ($requestKeys->contains('admin_id')) {
            $admin_id = User::find($request->input('admin_id'));
            $data['admin_id']  = $admin_id->admin_id;
        }
        if ($requestKeys->contains('distributor_id')) {
            $distributor_id = User::find($request->input('distributor_id'));
            $data['distributor_id']  = $distributor_id->distributor_id;
        }
        if ($requestKeys->contains('dealer_id')) {
            $dealer_id = User::find($request->input('dealer_id'));
            $data['dealer_id']  = $dealer_id->dealer_id;
        }
        if ($requestKeys->contains('subdealer_id')) {
            $subdealer_id = User::find($request->input('subdealer_id'));
            $data['subdealer_id']  = $subdealer_id->subdealer_id;
        }
        if ($requestKeys->contains('client_id')) {
            $client_id = User::find($request->input('client_id'));
            $data['client_id']  = $client_id->client_id;
        }

        $plan_id = $request->input('plan_id');
        $data['created_by'] = $request->input('user_id');

        $data['sim_id'] = $request->input('sim_id');
        $data['device_id'] = $request->input('device_id');
        $data['vehicle_type_id'] = $request->input('vehicle_type_id');
        $data['vehicle_name'] = $request->input('vehicle_name');

        $data['registration_number'] =  $request->input('vehicle_name');
        $data['install_person_name'] = $request->input('install_person_name');
        $data['service_person_name'] = $request->input('service_person_name');
        $data['description'] = $request->input('description');
        // $mytime = Carbon::now();
        // echo $mytime->toDateString();
        $data['installation_date'] = $request->input('installation_date');
        $data['expire_date'] = $request->input('expire_date');
        $data['extend_date'] = $request->input('extend_date');
        $data['vehicle_expire_date'] = $request->input('vehicle_expire_date');


        $data['due_amount'] = "0";


        $result = Point::where('total_point', '>', 0)
            ->where('admin_id', $data['admin_id'])
            ->where('distributor_id', $data['distributor_id'])
            ->where('dealer_id', $data['dealer_id'])
            ->where('subdealer_id', $data['subdealer_id'])
            ->where('plan_id', $plan_id)
            ->where('point_type_id', "1")
            ->where('status', 1)
            ->first();

        if (!empty($result)) {
            //Points
            $result->total_point = $result->total_point - 1;
            $result->save();

            // $plan_id = $result->plan_id;
            // $plan = Plan::find($plan_id);
            // $period_id = $plan->period_id;
            // $period = Period::find($period_id);

            // $newstart_date = Carbon::createFromFormat('Y-m-d', $request->input('installation_date')); // Create a Carbon instance from the given date
            // $newDateTime = $newstart_date->addDays($period->period_days); // Add 10 days
            // $data['expire_date'] = $newDateTime->format('Y-m-d'); // Format the result as yyyy-mm-dd
            // $data['installation_date'] = $request->input('installation_date');

            $newstart_date = Carbon::createFromFormat('Y-m-d', $request->input('vehicle_expire_date')); // Create a Carbon instance from the given date
            $newDateTime = $newstart_date->addDays(15); // Add 10 days
            $data['vehicle_extend_date'] = $newDateTime->format('Y-m-d'); // Format the result as yyyy-mm-dd

            // return response()->json($data, 200);

            //Main Vehicles
            $vehicle = new Vehicle($data);
            $result = $vehicle->save();

            //Licenses
            License::where('id', $request->input('license_id'))->update([
                'vehicle_id' => $vehicle->id,
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
                'device_imei' => $vehicle->device_imei
            );
            DB::table('configurations')->insert($main_config_details);

            $temp_vehicles = array(
                'device_imei' => $vehicle->device_imei,
                'device_make_id' => $vehicle->device_make_id,
                'device_model_id' => $vehicle->device_model_id
            );
            DB::table('configurations')->insert($temp_vehicles);

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
                'install_person_name' => $vehicleArray['install_person_name'],
                'service_person_name' => $vehicleArray['service_person_name'],
                'description' => $vehicleArray['description'],
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

            $response = ["success" => true, "message" => "Vehicle Inserted Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "No License Available", "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            $response = ["success" => false, "message" => "No Vehicle Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $vehicle, "status_code" => 200];
            return response()->json($response, 200);
        }
    }


    public function vehicle_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = User::find($user_id);

        if (!$user) {
            $response = ["success" => false, "message" => "User not found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $role_id = $user->role_id;

        $query = Vehicle::select(
            'vehicles.id',
            'vehicle_types.vehicle_type',
            'vehicles.vehicle_type_id',
            'vehicles.vehicle_name',
            'vehicles.sim_mob_no',
            'c.device_make',
            'd.device_model',
            'vehicles.device_imei',
            'vehicles.license_no',
            'vehicles.installation_date',
            'vehicles.expire_date',
            'clients.client_name',
            'e.speed_limit'
        )
            ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
            ->join('device_makes as c', 'vehicles.device_make_id', '=', 'c.id')
            ->join('device_models as d', 'vehicles.device_model_id', '=', 'd.id')
            ->join('configurations as e', 'vehicles.id', '=', 'e.vehicle_id')
            ->join('clients', 'vehicles.client_id', '=', 'clients.id')
            ->where('vehicles.status', 1);

        switch ($role_id) {
            case 1:
                break; // No additional filters needed
            case 2:
                $query->where('vehicles.admin_id', $user->admin_id);
                break;
            case 3:
                $query->where('vehicles.distributor_id', $user->distributor_id);
                break;
            case 4:
                $query->where('vehicles.dealer_id', $user->dealer_id);
                break;
            case 5:
                $query->where('vehicles.subdealer_id', $user->subdealer_id);
                break;
            case 6:
                $query->where('vehicles.client_id', $user->client_id);
                break;
            default:
                $response = ["success" => false, "message" => "Invalid user role", "status_code" => 400];
                return response()->json($response, 400);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            $response = ["success" => false, "message" => "No Vehicles Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $data, "status_code" => 200];
            return response()->json($response, 200);
        }
    }


    public function report_vehicle_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = User::find($user_id);

        if (!$user) {
            $response = ["success" => false, "message" => "User not found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $role_id = $user->role_id;

        $query = Vehicle::select(
            'vehicles.id',
            'vehicle_types.vehicle_type',
            'vehicles.vehicle_type_id',
            'vehicles.vehicle_name',
            'vehicles.sim_mob_no',
            'c.device_make',
            'd.device_model',
            'vehicles.device_imei',
            'vehicles.license_no',
            'vehicles.installation_date',
            'vehicles.expire_date',
            'clients.client_name',
            'e.speed_limit'
        )
            ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
            ->join('device_makes as c', 'vehicles.device_make_id', '=', 'c.id')
            ->join('device_models as d', 'vehicles.device_model_id', '=', 'd.id')
            ->join('configurations as e', 'vehicles.id', '=', 'e.vehicle_id')
            ->join('clients', 'vehicles.client_id', '=', 'clients.id');

        switch ($role_id) {
            case 1:
                break; // No additional filters needed
            case 2:
                $query->where('vehicles.admin_id', $user->admin_id);
                break;
            case 3:
                $query->where('vehicles.distributor_id', $user->distributor_id);
                break;
            case 4:
                $query->where('vehicles.dealer_id', $user->dealer_id);
                break;
            case 5:
                $query->where('vehicles.subdealer_id', $user->subdealer_id);
                break;
            case 6:
                $query->where('vehicles.client_id', $user->client_id);
                break;
            default:
                $response = ["success" => false, "message" => "Invalid user role", "status_code" => 400];
                return response()->json($response, 400);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            $response = ["success" => false, "message" => "No Vehicles Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $data, "status_code" => 200];
            return response()->json($response, 200);
        }
    }


    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return $this->sendError('Vehicle Not Found');
        }
        $old_sim_id = $vehicle->sim_id;
        $old_device_id = $vehicle->device_id;

        $validator = Validator::make($request->all(), [
            'vehicle_name' => 'required|max:255',
        ]);

        if ($old_sim_id != $request->input('sim_id')) {
            Sim::where('id', $old_sim_id)->update(['client_id' => null]);
            Sim::where('id', $request->input('sim_id'))->update(['client_id' => $request->input('client_id')]);
        }
        if ($old_device_id != $request->input('device_id')) {
            Device::where('id', $old_device_id)->update(['client_id' => null]);
            Device::where('id', $request->input('device_id'))->update(['client_id' => $request->input('client_id')]);
        }

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle->update($request->all())) {
            return $this->sendSuccess("Vehicle Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle');
        }
    }

    public function destroy(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return $this->sendError('Vehicle Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle->status = 0;
        $vehicle->deleted_by = $request->deleted_by;
        $vehicle->save();
        if ($vehicle->delete()) {
            return $this->sendSuccess('Vehicle Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Vehicle');
        }
    }

    public function change_vehicletype(Request $request, $device_imei)
    {
        $vehicle = Vehicle::where('device_imei', $device_imei)->first();

        if (!$vehicle) {
            $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($vehicle->update($request->all())) {

            $updatedCount = DB::table('twings.vehicles')
                ->where('device_imei', $device_imei)
                ->update(['vehicle_type_id' => $request->input('vehicle_type_id')]);
            if ($updatedCount) {
                $response = ["success" => true, "message" => "Vehicle Type Updated Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Update Vehicle Type", "status_code" => 404];
                return response()->json($response, 404);
            }
        } else {
            $response = ["success" => false, "message" => "Failed to Update Vehicle Type", "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function change_sim(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'network_id' => 'required|max:255',
                'sim_imei_no' => 'required|unique:sims,sim_imei_no',
                'sim_mob_no1' => 'required|unique:sims,sim_mob_no1'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $vehicle = Vehicle::find($request->input('id'));

            $data['network_id'] =  $request->input('network_id');
            $data['sim_imei_no'] =  $request->input('sim_imei_no');
            $data['sim_mob_no1'] =  $request->input('sim_mob_no1');
            $data['sim_mob_no2'] =  $request->input('sim_mob_no2');

            $data['admin_id'] = $vehicle->admin_id;
            $data['distributor_id'] = $vehicle->distributor_id;
            $data['dealer_id'] = $vehicle->dealer_id;
            $data['subdealer_id'] = $vehicle->subdealer_id;
            $data['client_id'] = $vehicle->client_id;

            $data['created_by'] = auth()->user()->id;
            $data['purchase_date'] = date('Y-m-d');

            $sim = new Sim($data);

            if ($sim->save()) {

                DB::table('sims')
                    ->where('sim_mob_no1', $vehicle->sim_mob_no)
                    ->update(['client_id' => NULL]);

                DB::table('vehicles')
                    ->where('id', $request->input('id'))
                    ->update(['sim_mob_no' => $request->input('sim_mob_no1'), 'sim_id' => $sim->id]);

                $response = ["success" => true, "message" => "Sim Updated Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Insert Sim", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }


    public function change_device(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required|max:255',
                'device_make_id' => 'required',
                'device_model_id' => 'required',
                'device_imei_no' => 'required|unique:devices,device_imei_no'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $vehicle = Vehicle::find($request->input('id'));

            $data['supplier_id'] =  $request->input('supplier_id');
            $data['device_make_id'] =  $request->input('device_make_id');
            $data['device_model_id'] =  $request->input('device_model_id');
            $data['device_imei_no'] =  $request->input('device_imei_no');
            $data['uid'] =  $request->input('uid');
            $data['ccid'] =  $request->input('ccid');
            $data['description'] =  $request->input('description');
            $data['admin_id'] = $vehicle->admin_id;
            $data['distributor_id'] = $vehicle->distributor_id;
            $data['dealer_id'] = $vehicle->dealer_id;
            $data['subdealer_id'] = $vehicle->subdealer_id;
            $data['client_id'] = $vehicle->client_id;
            $data['created_by'] = auth()->user()->id;
            $data['purchase_date'] = date('Y-m-d');

            $device = new Device($data);

            if ($device->save()) {
                $vehicle_data = array(
                    'vehicle_type_id' => $vehicle->vehicle_type_id,
                    'vehicle_name' => $vehicle->vehicle_name,
                    'device_id' => $device->id,
                    'device_imei' => $device->device_imei_no,
                    'sim_id' => $vehicle->sim_id,
                    'sim_mob_no' => $vehicle->sim_mob_no,
                    'device_make_id' => $vehicle->device_make_id,
                    'device_model_id' => $vehicle->device_model_id,
                    'installation_date' => $vehicle->installation_date,
                    'install_person_name' => $vehicle->install_person_name,
                    'service_person_name' => $vehicle->service_person_name,
                    'description' => $vehicle->description,
                    'expire_date' => $vehicle->expire_date,
                    'extend_date' => $vehicle->extend_date,
                    'vehicle_expire_date' => $vehicle->vehicle_expire_date,
                    'vehicle_extend_date' => $vehicle->vehicle_extend_date,
                    'admin_id' => $vehicle->admin_id,
                    'distributor_id' => $vehicle->distributor_id,
                    'dealer_id' => $vehicle->dealer_id,
                    'subdealer_id' => $vehicle->subdealer_id,
                    'client_id' => $vehicle->client_id,
                    'created_by' => $vehicle->created_by
                );
                DB::table('vehicles')->insert($vehicle_data);

                $new_data =  Vehicle::where('device_id', $device->id)->first();
                License::where('vehicle_id', $vehicle->id)->update([
                    'vehicle_id' => $new_data->id
                ]);
                Vehicle::where('id', $request->input('id'))->update([
                    'status' => 0
                ]);

                $vehicle = Vehicle::find($vehicle->id);
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

                DB::connection($connectionName)->table('vehicles')->insert($vehicle_data);
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


                $response = ["success" => true, "message" => "Device Updated Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Update Device", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }
    public function customer_vehicle_update(Request $request)
    {
        $id = $request->input('id');
        $vehicle = Vehicle::find($id);
        $client_id = $vehicle->client_id;
        $vehicle_type_id = $request->input('vehicle_type_id');
        $vehicle_name = $request->input('vehicle_name');

        DB::table('vehicles')
            ->where('id', $id)
            ->where('client_id', $client_id)
            ->update([
                'vehicle_type_id' => $vehicle_type_id,
                'vehicle_name' => $vehicle_name,
            ]);

        DB::table('live_data')
            ->where('vehicle_id', $id)
            ->update([
                'vehicle_name' => $vehicle_name,
            ]);

        DB::table('configurations')
            ->where('vehicle_id', $id)
            ->update([
                'vehicle_name' => $vehicle_name,
            ]);

        $result = CustomerConfiguration::where('client_id', $client_id)
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
            'vehicle_type_id' => $vehicle_type_id,
            'vehicle_name' => $vehicle_name
        );
        DB::connection($connectionName)->table('vehicles')->where('id', $id)->update($client_vehicle_data);

        $live_data = array(
            'vehicle_name' => $vehicle_name
        );
        DB::connection($connectionName)->table('live_data')->where('vehicle_id', $id)->update($live_data);

        $config_details = array(
            'vehicle_name' => $vehicle_name
        );
        DB::connection($connectionName)->table('configurations')->where('vehicle_id', $id)->update($config_details);

        DB::disconnect($connectionName);
    }


    public function customer_vehicle_delete(Request $request)
    {
        $id = $request->input('id');
        $vehicle = Vehicle::find($id);
        $client_id = $vehicle->client_id;

        DB::table('vehicles')
            ->where('id', $id)
            ->where('client_id', $client_id)
            ->update([
                'status' => 0,
            ]);

        DB::table('live_data')
            ->where('vehicle_id', $id)
            ->update([
                'vehicle_status' => 0,
            ]);

        $result = CustomerConfiguration::where('client_id', $client_id)
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
            'status' => 0,
        );
        DB::connection($connectionName)->table('vehicles')->where('id', $id)->update($client_vehicle_data);

        $live_data = array(
            'vehicle_status' => 0
        );
        DB::connection($connectionName)->table('live_data')->where('vehicle_id', $id)->update($live_data);

        DB::disconnect($connectionName);
    }

    public function change_live_data()
    {
        $clients =  DB::table('customer_configurations')->pluck('client_id');

        foreach ($clients as $client_id) {
            $result = CustomerConfiguration::where('client_id', $client_id)
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

            $live_data = array(
                'ac_status' => null,
                'temperature' => null,
                'door_status' => null
            );
            DB::connection($connectionName)->table('live_data')->update($live_data);

            DB::disconnect($connectionName);
        }
    }

    public function due_vehicle_list(Request $request)
    {
        $client_id = $request->input('client_id');

        $result = DB::table('vehicles')->select('id', 'vehicle_name', 'device_imei', 'sim_mob_no', 'installation_date', 'vehicle_expire_date', 'due_amount')
            ->where('client_id',  $client_id)
            ->where('due_amount', '>', 0)
            ->get();

        if ($result->isEmpty()) {
            $response = ["success" => false, "message" => "No Due Payment Vehicle", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        }
    }
    public function vehicle_due_update(Request $request)
    {
        $id =  $request->input('id');
        $vehicle = Vehicle::find($id);
        $vehicle->due_amount =  $vehicle->due_amount - $request->input('due_amount');
        $result =  $vehicle->save();

        if ($result) {
            $response = ["success" => true, "message" => "Due Payment Updated", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Error to Update Due Payment", "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
