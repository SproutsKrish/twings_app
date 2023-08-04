<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Device;


class DeviceController extends BaseController
{
    public function index()
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            return $this->sendError('No Devices Found');
        }

        return $this->sendSuccess($devices);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_imei_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device = new Device($request->all());
        if ($device->save()) {
            return $this->sendSuccess("Device Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Device');
        }
    }

    public function show($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        return $this->sendSuccess($device);
    }

    public function update(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        $validator = Validator::make($request->all(), [
            'device_imei_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($device->update($request->all())) {
            return $this->sendSuccess("Device Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device');
        }
    }

    public function destroy(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device->status = 0;
        $device->deleted_by = $request->deleted_by;
        $device->save();
        if ($device->delete()) {
            return $this->sendSuccess('Device Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Device');
        }
    }

    public function device_assign(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        if ($device->update($request->all())) {
            return $this->sendSuccess("Device Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device');
        }
    }
}
