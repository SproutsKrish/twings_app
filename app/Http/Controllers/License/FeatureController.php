<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Feature;

class FeatureController extends BaseController
{
    public function index()
    {
        $features = Feature::all();

        if ($features->isEmpty()) {
            return $this->sendError('No Features Found');
        }

        return $this->sendSuccess($features);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $feature = new Feature($request->all());
        if ($feature->save()) {
            return $this->sendSuccess("Feature Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Feature');
        }
    }

    public function show($id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return $this->sendError('Feature Not Found');
        }

        return $this->sendSuccess($feature);
    }

    public function update(Request $request, $id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return $this->sendError('Feature Not Found');
        }

        $validator = Validator::make($request->all(), [
            'feature_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($feature->update($request->all())) {
            return $this->sendSuccess("Feature Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Feature');
        }
    }

    public function destroy(Request $request, $id)
    {
        $feature = Feature::find($id);

        if (!$feature) {
            return $this->sendError('Feature Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $feature->status = 0;
        $feature->deleted_by = $request->deleted_by;
        $feature->save();
        if ($feature->delete()) {
            return $this->sendSuccess('Feature Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Feature');
        }
    }
}
