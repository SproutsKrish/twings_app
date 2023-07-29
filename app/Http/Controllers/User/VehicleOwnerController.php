<?php

namespace App\Http\Controllers\User;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\VehicleOwner;

class VehicleOwnerController extends BaseController
{
    public function index()
    {
        $vehicle_owners = VehicleOwner::all();

        if ($vehicle_owners->isEmpty()) {
            return $this->sendError('No Vehicle Owners Found');
        }

        return $this->sendSuccess($vehicle_owners);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_owner_company' => 'required|max:255',
            'vehicle_owner_name' => 'required|max:255',
            'vehicle_owner_email' => 'required|max:255',
            'vehicle_owner_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_owner = new VehicleOwner($request->all());
        if ($vehicle_owner->save()) {
            return $this->sendSuccess("Vehicle Owner Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Vehicle Owner');
        }
    }

    public function show($id)
    {
        $vehicle_owner = VehicleOwner::find($id);

        if (!$vehicle_owner) {
            return $this->sendError('Vehicle Owner Not Found');
        }

        return $this->sendSuccess($vehicle_owner);
    }

    public function update(Request $request, $id)
    {
        $vehicle_owner = VehicleOwner::find($id);

        if (!$vehicle_owner) {
            return $this->sendError('Vehicle Owner Not Found');
        }

        $validator = Validator::make($request->all(), [
            'vehicle_owner_company' => 'required|max:255',
            'vehicle_owner_name' => 'required|max:255',
            'vehicle_owner_email' => 'required|max:255',
            'vehicle_owner_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($vehicle_owner->update($request->all())) {
            return $this->sendSuccess("Vehicle Owner Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Vehicle Owner');
        }
    }

    public function destroy(Request $request, $id)
    {
        $vehicle_owner = VehicleOwner::find($id);

        if (!$vehicle_owner) {
            return $this->sendError('Vehicle Owner Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $vehicle_owner->status = 0;
        $vehicle_owner->deleted_by = $request->deleted_by;
        $vehicle_owner->save();
        if ($vehicle_owner->delete()) {
            return $this->sendSuccess('Vehicle Owner Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Vehicle Owner');
        }
    }
}
