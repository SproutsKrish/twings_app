<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Configuration;
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
        $vehicle = Vehicle::find($id);

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
            return $this->sendSuccess("Vehicle Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle');
        }
    }
    public function immobilizer_option(Request $request, $id)
    {

        $vehicle = Vehicle::find($id);

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
            return $this->sendSuccess("Vehicle Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle');
        }
    }
    public function odometer_update(Request $request, $id)
    {
        $vehicle = LiveData::where('vehicle_id', $id)->first();

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
        $vehicle = Configuration::where('vehicle_id', $id)->first();

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
