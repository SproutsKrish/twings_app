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
            'installation_date' => 'required',
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

            $plan_id = $result->plan_id;
            $plan = Plan::find($plan_id);
            $period_id = $plan->period_id;
            $period = Period::find($period_id);

            $newstart_date = Carbon::createFromFormat('Y-m-d', $request->input('installation_date')); // Create a Carbon instance from the given date
            $newDateTime = $newstart_date->addDays($period->period_days); // Add 10 days
            $data['expire_date'] = $newDateTime->format('Y-m-d'); // Format the result as yyyy-mm-dd
            $data['installation_date'] = $request->input('installation_date');

            //Main Vehicles
            $vehicle = new Vehicle($data);
            $result = $vehicle->save();

            //Licenses
            License::where('id', $request->input('license_id'))->update([
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
                'install_person_name' => $vehicleArray['install_person_name'],
                'service_person_name' => $vehicleArray['service_person_name'],
                'description' => $vehicleArray['description'],
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
            return $this->sendError('Vehicle Not Found');
        }

        return $this->sendSuccess($vehicle);
    }


    public function vehicle_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $data = User::find($user_id);
        $role_id = $data->role_id;

        if ($role_id == 1) {
            $data = Vehicle::select(
                'vehicles.id',
                'vehicle_types.vehicle_type',
                'vehicles.vehicle_name',
                'vehicles.sim_mob_no',
                'vehicles.device_imei',
                'vehicles.license_no',
                'packages.package_name',
                'periods.period_name',
                'vehicles.installation_date',
                'vehicles.expire_date',
                'dealers.dealer_name',
                'sub_dealers.subdealer_name',
                'clients.client_name'
            )
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->join('licenses', 'vehicles.license_no', '=', 'licenses.license_no')
                ->join('plans', 'licenses.plan_id', '=', 'plans.id')
                ->join('packages', 'plans.package_id', '=', 'packages.id')
                ->join('periods', 'plans.period_id', '=', 'periods.id')
                ->join('clients', 'vehicles.client_id', '=', 'clients.id')
                ->join('dealers', 'vehicles.dealer_id', '=', 'dealers.id')
                ->leftJoin('sub_dealers', 'vehicles.subdealer_id', '=', 'sub_dealers.id')
                ->get();
        } else if ($role_id == 2) {
            $data = User::find($user_id);
            $admin_id  = $data->admin_id;
            $data = Vehicle::select(
                'vehicles.id',
                'vehicle_types.vehicle_type',
                'vehicles.vehicle_name',
                'vehicles.sim_mob_no',
                'vehicles.device_imei',
                'vehicles.license_no',
                'packages.package_name',
                'periods.period_name',
                'vehicles.installation_date',
                'vehicles.expire_date',
                'dealers.dealer_name',
                'sub_dealers.subdealer_name',
                'clients.client_name'
            )
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->join('licenses', 'vehicles.license_no', '=', 'licenses.license_no')
                ->join('plans', 'licenses.plan_id', '=', 'plans.id')
                ->join('packages', 'plans.package_id', '=', 'packages.id')
                ->join('periods', 'plans.period_id', '=', 'periods.id')
                ->join('clients', 'vehicles.client_id', '=', 'clients.id')
                ->join('dealers', 'vehicles.dealer_id', '=', 'dealers.id')
                ->leftJoin('sub_dealers', 'vehicles.subdealer_id', '=', 'sub_dealers.id')
                ->where('vehicles.admin_id', $admin_id)
                ->get();
        } else if ($role_id == 3) {
            $data = User::find($user_id);
            $distributor_id  = $data->distributor_id;
            $data = Vehicle::select(
                'vehicles.id',
                'vehicle_types.vehicle_type',
                'vehicles.vehicle_name',
                'vehicles.sim_mob_no',
                'vehicles.device_imei',
                'vehicles.license_no',
                'packages.package_name',
                'periods.period_name',
                'vehicles.installation_date',
                'vehicles.expire_date',
                'dealers.dealer_name',
                'sub_dealers.subdealer_name',
                'clients.client_name'
            )
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->join('licenses', 'vehicles.license_no', '=', 'licenses.license_no')
                ->join('plans', 'licenses.plan_id', '=', 'plans.id')
                ->join('packages', 'plans.package_id', '=', 'packages.id')
                ->join('periods', 'plans.period_id', '=', 'periods.id')
                ->join('clients', 'vehicles.client_id', '=', 'clients.id')
                ->join('dealers', 'vehicles.dealer_id', '=', 'dealers.id')
                ->leftJoin('sub_dealers', 'vehicles.subdealer_id', '=', 'sub_dealers.id')
                ->where('vehicles.distributor_id', $distributor_id)
                ->get();
        } else if ($role_id == 4) {
            $data = User::find($user_id);
            $dealer_id  = $data->dealer_id;
            $data = Vehicle::select(
                'vehicles.id',
                'vehicle_types.vehicle_type',
                'vehicles.vehicle_name',
                'vehicles.sim_mob_no',
                'vehicles.device_imei',
                'vehicles.license_no',
                'packages.package_name',
                'periods.period_name',
                'vehicles.installation_date',
                'vehicles.expire_date',
                'dealers.dealer_name',
                'sub_dealers.subdealer_name',
                'clients.client_name'
            )
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->join('licenses', 'vehicles.license_no', '=', 'licenses.license_no')
                ->join('plans', 'licenses.plan_id', '=', 'plans.id')
                ->join('packages', 'plans.package_id', '=', 'packages.id')
                ->join('periods', 'plans.period_id', '=', 'periods.id')
                ->join('clients', 'vehicles.client_id', '=', 'clients.id')
                ->join('dealers', 'vehicles.dealer_id', '=', 'dealers.id')
                ->leftJoin('sub_dealers', 'vehicles.subdealer_id', '=', 'sub_dealers.id')
                ->where('vehicles.dealer_id', $dealer_id)
                ->get();
        } else if ($role_id == 5) {
            $data = User::find($user_id);
            $subdealer_id  = $data->subdealer_id;
            $data = Vehicle::select(
                'vehicles.id',
                'vehicle_types.vehicle_type',
                'vehicles.vehicle_name',
                'vehicles.sim_mob_no',
                'vehicles.device_imei',
                'vehicles.license_no',
                'packages.package_name',
                'periods.period_name',
                'vehicles.installation_date',
                'vehicles.expire_date',
                'dealers.dealer_name',
                'sub_dealers.subdealer_name',
                'clients.client_name'
            )
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->join('licenses', 'vehicles.license_no', '=', 'licenses.license_no')
                ->join('plans', 'licenses.plan_id', '=', 'plans.id')
                ->join('packages', 'plans.package_id', '=', 'packages.id')
                ->join('periods', 'plans.period_id', '=', 'periods.id')
                ->join('clients', 'vehicles.client_id', '=', 'clients.id')
                ->join('dealers', 'vehicles.dealer_id', '=', 'dealers.id')
                ->leftJoin('sub_dealers', 'vehicles.subdealer_id', '=', 'sub_dealers.id')
                ->where('vehicles.subdealer_id', $subdealer_id)
                ->get();
        } else if ($role_id == 6) {
            $data = User::find($user_id);
            $client_id  = $data->client_id;
            $data = Vehicle::select(
                'vehicles.id',
                'vehicle_types.vehicle_type',
                'vehicles.vehicle_name',
                'vehicles.sim_mob_no',
                'vehicles.device_imei',
                'vehicles.license_no',
                'packages.package_name',
                'periods.period_name',
                'vehicles.installation_date',
                'vehicles.expire_date',
                'dealers.dealer_name',
                'sub_dealers.subdealer_name',
                'clients.client_name'
            )
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->join('licenses', 'vehicles.license_no', '=', 'licenses.license_no')
                ->join('plans', 'licenses.plan_id', '=', 'plans.id')
                ->join('packages', 'plans.package_id', '=', 'packages.id')
                ->join('periods', 'plans.period_id', '=', 'periods.id')
                ->join('clients', 'vehicles.client_id', '=', 'clients.id')
                ->join('dealers', 'vehicles.dealer_id', '=', 'dealers.id')
                ->leftJoin('sub_dealers', 'vehicles.subdealer_id', '=', 'sub_dealers.id')
                ->where('vehicles.client_id', $client_id)
                ->get();
        }

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
}
