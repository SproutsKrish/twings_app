<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Configuration;
use App\Models\EnginePassword;
use App\Models\LiveData;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends BaseController
{
    public function store(Request $request)
    {
        $vehicle_id = $request->input('vehicle_id');
        $device_imei = $request->input('device_imei');

        $configurations = db::table('configurations')
            ->where('vehicle_id',  $vehicle_id)
            ->where('device_imei',  $device_imei)
            ->first();

        // return response()->json($configurations);

        $input = $request->all();
        $data = $configurations->update($input);

        if ($data) {
            return $this->sendSuccess("Vehicle Setting Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Setting ');
        }
    }
    public function show(Request $request)
    {
        $client_id = auth()->user()->client_id;

        return $this->sendSuccess($client_id);

        $configuration = Configuration::where('client_id', $client_id)
            ->where('vehicle_id', $request->input('vehicle_id'))
            ->get();

        if ($configuration->isEmpty()) {
            Configuration::create([
                'client_id' => $client_id,
                'vehicle_id' => $request->input('vehicle_id'),
            ]);
            $configuration = Configuration::where('client_id', $client_id)->get();
        }

        return $this->sendSuccess($configuration);
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
        $vehicle = Vehicle::where('device_imei', $device_imei)->first();

        if (!$vehicle) {
            $response = ["success" => false, "message" => "Vehicle Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'safe_parking' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($vehicle->update($request->all())) {
            $response = ["success" => false, "message" => "Safe Parking Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Failed to Update Safe Parking", "status_code" => 404];
            return response()->json($response, 404);
        }
    }
    public function immobilizer_option(Request $request, $device_imei)
    {
        $validator = Validator::make($request->all(), [
            'engine_password' => 'required',
        ]);

        $enginePasswords = EnginePassword::where('engine_password', $request->input('engine_password'))->first();

        if (!empty($enginePasswords)) {
            $vehicle = Vehicle::where('device_imei', $device_imei)->first();
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

            if ($vehicle->update($request->all())) {
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
            $response = ["success" => true, "message" => 'Vehicle Speed Limit Updated Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => true, "message" => 'Failed to Update Vehicle Speed Limit', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
