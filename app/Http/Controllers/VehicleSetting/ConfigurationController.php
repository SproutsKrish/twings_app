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
        $client_id = auth()->user()->client_id;

        Configuration::create([
            'client_id' => $client_id,
            'vehicle_id' => $request->input('vehicle_id'),
            'parking_alert_time' => $request->input('parking_alert_time'),
            'idle_alert_time' => $request->input('idle_alert_time'),
            'speed_limit' => $request->input('speed_limit'),
            'expected_mileage' => $request->input('expected_mileage'),
            'idle_rpm' => $request->input('idle_rpm'),
            'max_rpm' => $request->input('max_rpm'),
            'temp_low' => $request->input('temp_low'),
            'temp_high' => $request->input('temp_high'),
            'fuel_fill_limit' => $request->input('fuel_fill_limit'),
            'fuel_dip_limit' => $request->input('fuel_dip_limit')
        ]);

        return $this->sendSuccess("Configuration Updated Successfully");
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


    public function safe_parking(Request $request, $id)
    {
        $vehicle = Vehicle::where('device_imei', '=', $id)->first();

        if (!$vehicle) {
            return $this->sendError('Vehicle Not Found');
        }

        $validator = Validator::make($request->all(), [
            'safe_parking' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle->update($request->all())) {
            return $this->sendSuccess("Vehicle Safe Parking Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle');
        }
    }
    public function immobilizer_option(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'engine_password' => 'required|max:255',
        ]);

        $engine_password = $request->input('engine_password');
        $enginePasswords = EnginePassword::where('engine_password',  $engine_password)->first();

        if (!empty($enginePasswords)) {
            $vehicle = Vehicle::where('device_imei', '=', $id)->first();

            if (!$vehicle) {
                return $this->sendError('Vehicle Not Found');
            }

            $validator = Validator::make($request->all(), [
                'immobilizer_option' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            if ($vehicle->update($request->all())) {
                $response = ["success" => true, "message" => 'Engine Status Updated', "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => 'Failed to Update Engine Status', "status_code" => 404];
                return response()->json($response, 404);
            }
        } else {
            return $this->sendError('Password Is Incorrect');
        }
    }

    public function odometer_update(Request $request, $id)
    {
        $vehicle = LiveData::where('deviceimei', $id)->first();

        if (!$vehicle) {
            return $this->sendError('Vehicle Not Found');
        }

        $validator = Validator::make($request->all(), [
            'odometer' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle->update($request->all())) {
            return $this->sendSuccess("Vehicle Odometer Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Odometer');
        }
    }
    public function speed_update(Request $request, $id)
    {
        $vehicle = Configuration::where('device_imei', $id)->first();

        if (!$vehicle) {
            return $this->sendError('Vehicle Not Found');
        }

        $validator = Validator::make($request->all(), [
            'speed_limit' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle->update($request->all())) {
            return $this->sendSuccess("Vehicle Speed Limit Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Speed Limit');
        }
    }
}
