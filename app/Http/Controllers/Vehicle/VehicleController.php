<?php

namespace App\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Device;
use App\Models\Sim;
use Illuminate\Support\Facades\Validator;

use App\Models\Vehicle;

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
            'vehicle_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle = new Vehicle($request->all());
        if ($vehicle->save()) {

            Sim::where('id', $vehicle->sim_id)->update(['client_id' => $vehicle->client_id]);
            Device::where('id', $vehicle->device_id)->update(['client_id' => $vehicle->client_id]);

            return $this->sendSuccess("Vehicle Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Vehicle');
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
            return $this->sendSuccess("Vehicle Type Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Type');
        }
    }
}
