<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Camera;

class CameraController extends BaseController
{
    public function index()
    {
        $cameras = Camera::all();

        if ($cameras->isEmpty()) {
            return $this->sendError('No Cameras Found');
        }

        return $this->sendSuccess($cameras);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera = new Camera($request->all());
        if ($camera->save()) {
            return $this->sendSuccess("Camera Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Camera');
        }
    }

    public function show($id)
    {
        $camera = Camera::find($id);

        if (!$camera) {
            return $this->sendError('Camera Not Found');
        }

        return $this->sendSuccess($camera);
    }

    public function update(Request $request, $id)
    {
        $camera = Camera::find($id);

        if (!$camera) {
            return $this->sendError('Camera Not Found');
        }

        $validator = Validator::make($request->all(), [
            'serial_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($camera->update($request->all())) {
            return $this->sendSuccess("Camera Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Camera');
        }
    }

    public function destroy(Request $request, $id)
    {
        $camera = Camera::find($id);

        if (!$camera) {
            return $this->sendError('Camera Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera->status = 0;
        $camera->deleted_by = $request->deleted_by;
        $camera->save();
        if ($camera->delete()) {
            return $this->sendSuccess('Camera Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Camera');
        }
    }
}
