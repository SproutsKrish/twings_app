<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\CameraModel;

class CameraModelController extends BaseController
{
    public function index()
    {
        $camera_models = CameraModel::all();

        if ($camera_models->isEmpty()) {
            return $this->sendError('No Camera Models Found');
        }

        return $this->sendSuccess($camera_models);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_model' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera_model = new CameraModel($request->all());
        if ($camera_model->save()) {
            return $this->sendSuccess("Camera Model Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Camera Model');
        }
    }

    public function show($id)
    {
        $camera_model = CameraModel::find($id);

        if (!$camera_model) {
            return $this->sendError('Camera Model Not Found');
        }

        return $this->sendSuccess($camera_model);
    }

    public function update(Request $request, $id)
    {
        $camera_model = CameraModel::find($id);

        if (!$camera_model) {
            return $this->sendError('Camera Model Not Found');
        }

        $validator = Validator::make($request->all(), [
            'camera_model' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($camera_model->update($request->all())) {
            return $this->sendSuccess("Camera Model Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Camera Model');
        }
    }

    public function destroy(Request $request, $id)
    {
        $camera_model = CameraModel::find($id);

        if (!$camera_model) {
            return $this->sendError('Camera Model Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera_model->status = 0;
        $camera_model->deleted_by = $request->deleted_by;
        $camera_model->save();
        if ($camera_model->delete()) {
            return $this->sendSuccess('Camera Model Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Camera Model');
        }
    }
}
