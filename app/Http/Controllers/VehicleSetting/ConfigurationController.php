<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Configuration;
use Illuminate\Http\Request;

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
}
