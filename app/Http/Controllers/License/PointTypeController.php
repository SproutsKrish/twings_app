<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\PointType;

class PointTypeController extends BaseController
{
    public function index()
    {
        $point_types = PointType::all();

        if ($point_types->isEmpty()) {
            return $this->sendError('No Point Types Found');
        }

        return $this->sendSuccess($point_types);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'point_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $point_type = new PointType($request->all());
        if ($point_type->save()) {
            return $this->sendSuccess("Point Type Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Point Type');
        }
    }

    public function show($id)
    {
        $point_type = PointType::find($id);

        if (!$point_type) {
            return $this->sendError('Point Type Not Found');
        }

        return $this->sendSuccess($point_type);
    }

    public function update(Request $request, $id)
    {
        $point_type = PointType::find($id);

        if (!$point_type) {
            return $this->sendError('Point Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'point_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($point_type->update($request->all())) {
            return $this->sendSuccess("Point Type Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Point Type');
        }
    }

    public function destroy(Request $request, $id)
    {
        $point_type = PointType::find($id);

        if (!$point_type) {
            return $this->sendError('Point Type Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $point_type->status = 0;
        $point_type->deleted_by = $request->deleted_by;
        $point_type->save();
        if ($point_type->delete()) {
            return $this->sendSuccess('Point Type Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Point Type');
        }
    }
}
