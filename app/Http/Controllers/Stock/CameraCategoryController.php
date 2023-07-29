<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\CameraCategory;

class CameraCategoryController extends BaseController
{
    public function index()
    {
        $camera_categories = CameraCategory::all();

        if ($camera_categories->isEmpty()) {
            return $this->sendError('No Camera Categories Found');
        }

        return $this->sendSuccess($camera_categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'camera_category' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera_category = new CameraCategory($request->all());
        if ($camera_category->save()) {
            return $this->sendSuccess("Camera Category Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Camera Category');
        }
    }

    public function show($id)
    {
        $camera_category = CameraCategory::find($id);

        if (!$camera_category) {
            return $this->sendError('Camera Category Not Found');
        }

        return $this->sendSuccess($camera_category);
    }

    public function update(Request $request, $id)
    {
        $camera_category = CameraCategory::find($id);

        if (!$camera_category) {
            return $this->sendError('Camera Category Not Found');
        }

        $validator = Validator::make($request->all(), [
            'camera_category' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($camera_category->update($request->all())) {
            return $this->sendSuccess("Camera Category Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Camera Category');
        }
    }

    public function destroy(Request $request, $id)
    {
        $camera_category = CameraCategory::find($id);

        if (!$camera_category) {
            return $this->sendError('Camera Category Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $camera_category->status = 0;
        $camera_category->deleted_by = $request->deleted_by;
        $camera_category->save();
        if ($camera_category->delete()) {
            return $this->sendSuccess('Camera Category Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Camera Category');
        }
    }
}
