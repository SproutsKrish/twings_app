<?php

namespace App\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\VehicleType;

class VehicleTypeController extends BaseController
{
    public function index()
    {
        $vehicle_types = VehicleType::all();

        if ($vehicle_types->isEmpty()) {
            return $this->sendError('No Vehicle Types Found');
        }

        return $this->sendSuccess($vehicle_types);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_type = new VehicleType($request->all());
        if ($vehicle_type->save()) {
            return $this->sendSuccess("Vehicle Type Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Vehicle Type');
        }
    }

    public function show($id)
    {
        $vehicle_type = VehicleType::find($id);

        if (!$vehicle_type) {
            return $this->sendError('Vehicle Type Not Found');
        }

        return $this->sendSuccess($vehicle_type);
    }

    public function update(Request $request, $id)
    {
        $vehicle_type = VehicleType::find($id);

        if (!$vehicle_type) {
            return $this->sendError('Vehicle Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle_type->update($request->all())) {
            return $this->sendSuccess("Vehicle Type Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Type');
        }
    }

    public function destroy(Request $request, $id)
    {
        $vehicle_type = VehicleType::find($id);

        if (!$vehicle_type) {
            return $this->sendError('Vehicle Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_type->status = 0;
        $vehicle_type->deleted_by = $request->deleted_by;
        $vehicle_type->save();
        if ($vehicle_type->delete()) {
            return $this->sendSuccess('Vehicle Type Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Vehicle Type');
        }
    }
}
