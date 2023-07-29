<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\DeviceType;


class DeviceTypeController extends BaseController
{
    public function index()
    {
        $device_types = DeviceType::all();

        if ($device_types->isEmpty()) {
            return $this->sendError('No Device Types Found');
        }

        return $this->sendSuccess($device_types);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device_type = new DeviceType($request->all());
        if ($device_type->save()) {
            return $this->sendSuccess("Device Type Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Device Type');
        }
    }

    public function show($id)
    {
        $device_type = DeviceType::find($id);

        if (!$device_type) {
            return $this->sendError('Device Type Not Found');
        }

        return $this->sendSuccess($device_type);
    }

    public function update(Request $request, $id)
    {
        $device_type = DeviceType::find($id);

        if (!$device_type) {
            return $this->sendError('Device Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'device_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($device_type->update($request->all())) {
            return $this->sendSuccess("Device Type Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device Type');
        }
    }

    public function destroy(Request $request, $id)
    {
        $device_type = DeviceType::find($id);

        if (!$device_type) {
            return $this->sendError('Device Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device_type->status = 0;
        $device_type->deleted_by = $request->deleted_by;
        $device_type->save();
        if ($device_type->delete()) {
            return $this->sendSuccess('Device Type Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Device Type');
        }
    }
}
