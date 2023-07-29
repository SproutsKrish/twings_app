<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\CameraType;

class CameraTypeController extends BaseController
{
    public function index()
    {
        $camera_types = CameraType::all();

        if ($camera_types->isEmpty()) {
            return $this->sendError('No Camera Types Found');
        }

        return $this->sendSuccess($camera_types);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera_type = new CameraType($request->all());
        if ($camera_type->save()) {
            return $this->sendSuccess("Camera Type Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Camera Type');
        }
    }

    public function show($id)
    {
        $camera_type = CameraType::find($id);

        if (!$camera_type) {
            return $this->sendError('Camera Type Not Found');
        }

        return $this->sendSuccess($camera_type);
    }

    public function update(Request $request, $id)
    {
        $camera_type = CameraType::find($id);

        if (!$camera_type) {
            return $this->sendError('Camera Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'camera_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($camera_type->update($request->all())) {
            return $this->sendSuccess("Camera Type Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Camera Type');
        }
    }

    public function destroy(Request $request, $id)
    {
        $camera_type = CameraType::find($id);

        if (!$camera_type) {
            return $this->sendError('Camera Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera_type->status = 0;
        $camera_type->deleted_by = $request->deleted_by;
        $camera_type->save();
        if ($camera_type->delete()) {
            return $this->sendSuccess('Camera Type Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Camera Type');
        }
    }
}
