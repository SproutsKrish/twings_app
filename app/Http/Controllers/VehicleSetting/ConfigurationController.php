<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Configuration;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigurationController extends BaseController
{
    public function show(Request $request)
    {
        $configuration = Configuration::where('client_id', $request->input('client_id'))
            ->where('vehicle_id', $request->input('vehicle_id'))
            ->get();


        if ($configuration->isEmpty()) {
            Configuration::create([
                'client_id' => $request->input('client_id'),
                'vehicle_id' => $request->input('vehicle_id'),
            ]);
            $configuration = Configuration::where('client_id', $request->input('client_id'))->get();
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
}
