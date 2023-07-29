<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\DeviceCategory;

class DeviceCategoryController extends BaseController
{
    public function index()
    {
        $device_categories = DeviceCategory::all();

        if ($device_categories->isEmpty()) {
            return $this->sendError('No Device Categories Found');
        }

        return $this->sendSuccess($device_categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_category' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device_category = new DeviceCategory($request->all());
        if ($device_category->save()) {
            return $this->sendSuccess("Device Category Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Device Category');
        }
    }

    public function show($id)
    {
        $device_category = DeviceCategory::find($id);

        if (!$device_category) {
            return $this->sendError('Device Category Not Found');
        }

        return $this->sendSuccess($device_category);
    }

    public function update(Request $request, $id)
    {
        $device_category = DeviceCategory::find($id);

        if (!$device_category) {
            return $this->sendError('Device Category Not Found');
        }

        $validator = Validator::make($request->all(), [
            'device_category' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($device_category->update($request->all())) {
            return $this->sendSuccess("Device Category Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device Category');
        }
    }

    public function destroy(Request $request, $id)
    {
        $device_category = DeviceCategory::find($id);

        if (!$device_category) {
            return $this->sendError('Camera Category Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device_category->status = 0;
        $device_category->deleted_by = $request->deleted_by;
        $device_category->save();
        if ($device_category->delete()) {
            return $this->sendSuccess('Camera Category Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Camera Category');
        }
    }
}
