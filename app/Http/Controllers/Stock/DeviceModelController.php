<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\DeviceModel;

class DeviceModelController extends BaseController
{
    public function index()
    {
        $device_models = DeviceModel::all();

        if ($device_models->isEmpty()) {
            return $this->sendError('No Device Models Found');
        }

        return $this->sendSuccess($device_models);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_model' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device_model = new DeviceModel($request->all());
        if ($device_model->save()) {
            return $this->sendSuccess("Device Model Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Device Model');
        }
    }

    public function show($id)
    {
        $device_model = DeviceModel::find($id);

        if (!$device_model) {
            return $this->sendError('Device Model Not Found');
        }

        return $this->sendSuccess($device_model);
    }

    public function update(Request $request, $id)
    {
        $device_model = DeviceModel::find($id);

        if (!$device_model) {
            return $this->sendError('Device Model Not Found');
        }

        $validator = Validator::make($request->all(), [
            'device_model' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($device_model->update($request->all())) {
            return $this->sendSuccess("Device Model Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device Model');
        }
    }

    public function destroy(Request $request, $id)
    {
        $device_model = DeviceModel::find($id);

        if (!$device_model) {
            return $this->sendError('Device Model Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device_model->status = 0;
        $device_model->deleted_by = $request->deleted_by;
        $device_model->save();
        if ($device_model->delete()) {
            return $this->sendSuccess('Device Model Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Device Model');
        }
    }
}
