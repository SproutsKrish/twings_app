<?php

namespace App\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\VehicleService;

class VehicleServiceController extends BaseController
{
    public function index()
    {
        $vehicle_services = VehicleService::all();

        if ($vehicle_services->isEmpty()) {
            return $this->sendError('No Vehicle Services Found');
        }

        return $this->sendSuccess($vehicle_services);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_service = new VehicleService($request->all());
        if ($vehicle_service->save()) {
            return $this->sendSuccess("Vehicle Service Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Vehicle Service');
        }
    }

    public function show($id)
    {
        $vehicle_service = VehicleService::find($id);

        if (!$vehicle_service) {
            return $this->sendError('Vehicle Service Not Found');
        }

        return $this->sendSuccess($vehicle_service);
    }

    public function update(Request $request, $id)
    {
        $vehicle_service = VehicleService::find($id);

        if (!$vehicle_service) {
            return $this->sendError('Vehicle Service Not Found');
        }

        $validator = Validator::make($request->all(), [
            'service_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle_service->update($request->all())) {
            return $this->sendSuccess("Vehicle Service Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Service');
        }
    }

    public function destroy(Request $request, $id)
    {
        $vehicle_service = VehicleService::find($id);

        if (!$vehicle_service) {
            return $this->sendError('Vehicle Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_service->status = 0;
        $vehicle_service->deleted_by = $request->deleted_by;
        $vehicle_service->save();
        if ($vehicle_service->delete()) {
            return $this->sendSuccess('Vehicle Service Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Vehicle Service');
        }
    }
}
