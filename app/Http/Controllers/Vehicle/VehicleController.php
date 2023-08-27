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
            'vehicle_type_id' => 'required|max:255',
            'vehicle_name' => 'required|unique:vehicles,vehicle_name,max:255',
            'device_imei' => 'required|unique:vehicles,device_imei,max:255',
            'sim_mob_no' => 'required|unique:vehicles,sim_mob_no,max:255',
            'license_no' => 'required|max:255',
            'client_id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $admin_id = auth()->user()->admin_id;
        $distributor_id = auth()->user()->distributor_id;
        $dealer_id = auth()->user()->dealer_id;
        $subdealer_id = auth()->user()->subdealer_id;

        $plan_id = $request->input('plan_id');
        $point_type_id = $request->input('point_type_id');

        $result = Point::where('total_point', '>=', 1)
            ->where('admin_id', $admin_id)
            ->where('distributor_id', $distributor_id)
            ->where('dealer_id', $dealer_id)
            ->where('subdealer_id', $subdealer_id)
            ->where('plan_id', $plan_id)
            ->where('point_type_id', $point_type_id)
            ->where('status', 1)
            ->first();


        if (!empty($result)) {
            $result->total_point = $result->total_point - 1;
            $result->save();

            $plan_id = $result->plan_id;

            $plan = Plan::find($plan_id);
            $period_id = $plan->period_id;
            $period = Period::find($period_id);

            $data = $request->all();
            $installation_date = Carbon::now();
            $data['installation_date'] = $installation_date->format('Y-m-d H:i:s');
            $newstart_date = Carbon::now();
            $newDateTime = $newstart_date->addDays($period->period_days);
            $data['expire_date'] = $newDateTime->format('Y-m-d H:i:s');
            $data['ip_address'] = $request->ip_address;

            $data['admin_id'] = $admin_id;
            $data['distributor_id'] = $distributor_id;
            $data['dealer_id'] = $dealer_id;
            $data['subdealer_id'] = $subdealer_id;

            // dd($data);

            $vehicle = new Vehicle($data);
            $result = $vehicle->save();

            License::where('license_no', $request->input('license_no'))->update([
                'vehicle_id' => $vehicle->id,
                'start_date' => $vehicle->installation_date,
                'expiry_date' => $vehicle->expire_date,
                'client_id' => $vehicle->client_id
            ]);

            $vehicle = Vehicle::find($vehicle->id);
            $vehicleArray = $vehicle->toArray();

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
                // Add any other connection parameters you need
            ];


            Config::set("database.connections.$connectionName", $connectionConfig);
            DB::purge($connectionName);
            $client_vehicle_data = array(
                'id' => $vehicleArray['id'],
                'vehicle_type_id' => $vehicleArray['vehicle_type_id'],
                'vehicle_name' => $vehicleArray['vehicle_name'],
                'vehicle_make' => $vehicleArray['vehicle_make'],
                'vehicle_model' => $vehicleArray['vehicle_model'],
                'vehicle_year' => $vehicleArray['vehicle_year'],
                'device_id' => $vehicleArray['device_id'],
                'device_imei' => $vehicleArray['device_imei'],
                'sim_id' => $vehicleArray['sim_id'],
                'sim_mob_no' => $vehicleArray['sim_mob_no'],
                'license_no' => $vehicleArray['license_no'],
                'insurance_company' => $vehicleArray['insurance_company'],
                'insurance_number' => $vehicleArray['insurance_number'],
                'insurance_start_date' => $vehicleArray['insurance_start_date'],
                'insurance_expiry_date' => $vehicleArray['insurance_expiry_date'],
                'registration_number' => $vehicleArray['registration_number'],
                'chassis_number' => $vehicleArray['chassis_number'],
                'engine_number' => $vehicleArray['engine_number'],
                'engine_number' => $vehicleArray['engine_number'],
                'ownership_type' => $vehicleArray['ownership_type'],
                'fc_date' => $vehicleArray['fc_date'],
                'installation_date' => $vehicleArray['installation_date'],
                'expire_date' => $vehicleArray['expire_date'],
                'extend_date' => $vehicleArray['extend_date'],
                'immobilizer_option' => $vehicleArray['immobilizer_option'],
                'safe_parking' => $vehicleArray['safe_parking'],
                'admin_id' => $vehicleArray['admin_id'],
                'distributor_id' => $vehicleArray['distributor_id'],
                'dealer_id' => $vehicleArray['dealer_id'],
                'subdealer_id' => $vehicleArray['subdealer_id'],
                'client_id' => $vehicleArray['client_id']
            );
            DB::connection($connectionName)->table('vehicles')->insert($client_vehicle_data);
            $live_data = array(
                'client_id' => $vehicle->client_id,
                'vehicle_id' => $vehicle->id,
                'vehicle_name' => $vehicle->vehicle_name,
                'vehicle_current_status' => '4',
                'vehicle_status' => '1',
                'deviceimei' => $vehicle->device_imei
            );
            DB::connection($connectionName)->table('live_data')->insert($live_data);
            DB::disconnect($connectionName);

            Sim::where('id', $vehicle->sim_id)->update(['client_id' => $vehicle->client_id]);
            Device::where('id', $vehicle->device_id)->update(['client_id' => $vehicle->client_id]);

            return $this->sendSuccess("Vehicle Inserted Successfully");
        } else {
            return $this->sendError("License Createds Failed");
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

    public function change_vehicletype(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return $this->sendError('Vehicle Not Found');
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle->update($request->all())) {

            //     $dbName = 'twings';

            //     // Use the database name to specify the table
            //     $updatedCount = DB::connection($dbName)
            //         ->table('vehicles')
            //         ->where('id', $id)
            //         ->update(['vehicle_type_id' => $request->input('vehicle_type_id')]);

            //     if ($updatedCount) {
            //         return $this->sendSuccess("Vehicle Type Updated Successfully");
            //     } else {
            //         return $this->sendError('Failed to Update Vehicle Type');
            //     }

            return $this->sendSuccess("Vehicle Type Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Type');
        }
    }
}
