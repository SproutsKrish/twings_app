<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Configuration;
use App\Models\CustomerConfiguration;
use App\Models\EnginePassword;
use App\Models\ImmoblizerData;
use App\Models\LiveData;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class ConfigurationController extends BaseController
{
    public function store(Request $request)
    {
        $vehicle_id = $request->input('vehicle_id');
        $device_imei = $request->input('device_imei');

        $configuration = Configuration::where('vehicle_id', $vehicle_id)
            ->where('device_imei', $device_imei)
            ->first();

        if (!$configuration) {
            return response()->json(['message' => 'Configuration not found'], 404);
        }

        $input = $request->all();

        // Update the configuration with the new data
        $data =   $configuration->update($input);

        $result = CustomerConfiguration::where('client_id', $configuration->client_id)
            ->first();

        // Specify the dynamic connection configuration
        $connectionName = $result->db_name;
        $connectionConfig = [
            'driver' => 'mysql',
            'host' => env('DB_HOST'), // Use the environment variable for host
            'port' => env('DB_PORT'), // Use the environment variable for port
            'database' => $result->db_name,   // Change this to the actual database name
            'username' => env('DB_USERNAME'), // Use the environment variable for username
            'password' => env('DB_PASSWORD'), // Use the environment variable for password
            // Add any other connection parameters you need
        ];

        // Use the dynamic connection configuration to connect to the database
        Config::set("database.connections.$connectionName", $connectionConfig);
        DB::purge($connectionName); // Clear the connection cache

        DB::connection($connectionName)->table('configurations')->where('device_imei', $configuration->device_imei)->update($input);

        if (empty($result)) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => "Configuration Updated Successfully", "status_code" => 200];
            return response()->json($response, 200);
        }
    }

    public function store_all(Request $request)
    {

        $vehicleIds = $request->input('vehicle_id');
        $count = count($vehicleIds);

        for ($i = 0; $i < $count; $i++) {
            $vehicle_id = $vehicleIds[$i];

            $configuration = Configuration::where('vehicle_id', $vehicle_id)
                ->first();

            if (!$configuration) {
                return response()->json(['message' => 'Configuration not found'], 404);
            }

            $input = $request->all();
            unset($input['vehicle_id']);

            // Update the configuration with the new data
            $data =   $configuration->update($input);

            $result = CustomerConfiguration::where('client_id', $configuration->client_id)
                ->first();

            // Specify the dynamic connection configuration
            $connectionName = $result->db_name;
            $connectionConfig = [
                'driver' => 'mysql',
                'host' => env('DB_HOST'), // Use the environment variable for host
                'port' => env('DB_PORT'), // Use the environment variable for port
                'database' => $result->db_name,   // Change this to the actual database name
                'username' => env('DB_USERNAME'), // Use the environment variable for username
                'password' => env('DB_PASSWORD'), // Use the environment variable for password
                // Add any other connection parameters you need
            ];

            // Use the dynamic connection configuration to connect to the database
            Config::set("database.connections.$connectionName", $connectionConfig);
            DB::purge($connectionName); // Clear the connection cache

            DB::connection($connectionName)->table('configurations')->where('device_imei', $configuration->device_imei)->update($input);
        }

        return response()->json("OK");
    }


    public function show(Request $request)
    {
        $configuration = Configuration::where('device_imei', $request->input('device_imei'))
            ->where('vehicle_id', $request->input('vehicle_id'))
            ->first();

        if (empty($configuration)) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $configuration, "status_code" => 200];
            return response()->json($response, 200);
        }
    }

    public function update(Request $request, $id)
    {
        $configuration = Configuration::find($id);

        if (!$configuration) {
            return $this->sendError('Configuration Not Found');
        }

        if ($configuration->update($request->all())) {
            return $this->sendSuccess("Configuration Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Configuration');
        }
    }
    public function safe_parking(Request $request, $device_imei)
    {
        $livedata = LiveData::where('device_imei', $device_imei)->where('vehicle_status', 1)->first();

        if (!$livedata) {
            $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'safe_parking' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        } else {
            $livedata->safe_parking = $request->input('safe_parking');
            $livedata->safe_parking_alert = 0;
            $res = $livedata->update();
            if ($res) {
                $response = ["success" => false, "message" => "Safe Parking Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Update Safe Parking", "status_code" => 404];
                return response()->json($response, 404);
            }
        }
    }

    public function immobilizer_option(Request $request, $device_imei)
    {

        $today = Carbon::now();

        $validator = Validator::make($request->all(), [
            'engine_password' => 'required',
        ]);

        $enginePasswords = EnginePassword::where('engine_password', $request->input('engine_password'))->first();

        if (!empty($enginePasswords)) {
            $vehicle = Vehicle::where('device_imei', $device_imei)->first();
            $user = User::where('client_id', $vehicle->client_id)->first();

            if (!$vehicle) {
                $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
                return response()->json($response, 404);
            }

            $validator = Validator::make($request->all(), [
                'immobilizer_option' => 'required',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $data = array(
                'client_id' => $vehicle->client_id,
                'user_id' => $user->id,
                'deviceimei' => $vehicle->device_imei,
                'vehicle_id' => $vehicle->id,
                'status' => $request->input('immobilizer_option'),
                'completed_status' => '1',
                'dealer_id' => $vehicle->dealer_id,
                'subdealer_id' => $vehicle->subdealer_id,
                'created_by' => auth()->user()->id,
                'created_on' => $today
            );

            $res = ImmoblizerData::create($data);

            if ($res) {
                $vehicle->immobilizer_option = $request->input('immobilizer_option');
                $vehicle->save();
                DB::table('twings.vehicles')->where('id', $vehicle->id)->update(['immobilizer_option' => $request->input('immobilizer_option')]);
                $response = ["success" => true, "message" => 'Engine Status Updated Successfully', "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => 'Failed to Update Engine Status', "status_code" => 404];
                return response()->json($response, 404);
            }
        } else {
            $response = ["success" => false, "message" => 'Password Is Incorrect', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
    public function odometer_update(Request $request, $deviceimei)
    {
        $vehicle = LiveData::where('deviceimei', $deviceimei)->first();

        if (!$vehicle) {
            $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'odometer' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($vehicle->update($request->all())) {
            $response = ["success" => true, "message" => 'Vehicle Odometer Updated Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {

            $response = ["success" => false, "message" => 'Failed to Update Vehicle Odometer', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
    public function speed_update(Request $request, $device_imei)
    {
        $vehicle = Configuration::where('device_imei', $device_imei)->first();

        if (!$vehicle) {
            $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'speed_limit' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($vehicle->update($request->all())) {

            $main_vehicle = DB::table('twings.configurations')->where('device_imei', $device_imei)->update(['speed_limit' => $request->input('speed_limit')]);

            $response = ["success" => true, "message" => 'Vehicle Speed Limit Updated Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => true, "message" => 'Failed to Update Vehicle Speed Limit', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
