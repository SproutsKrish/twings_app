<?php

namespace App\Http\Controllers\License;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Period;

class PeriodController extends BaseController
{
    public function index()
    {
        $periods = Period::all();

        if ($periods->isEmpty()) {
            return $this->sendError('No Periods Found');
        }

        return $this->sendSuccess($periods);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period_name' => 'required|max:255',
            'description' => 'required|max:255',
            'period_days' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $period = new Period($request->all());
        if ($period->save()) {
            return $this->sendSuccess("Period Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Period');
        }
    }

    public function show($id)
    {
        $period = Period::find($id);

        if (!$period) {
            return $this->sendError('Period Not Found');
        }

        return $this->sendSuccess($period);
    }

    public function update(Request $request, $id)
    {
        $period = Period::find($id);

        if (!$period) {
            return $this->sendError('Period Not Found');
        }

        $validator = Validator::make($request->all(), [
            'period_name' => 'required|max:255',
            'description' => 'required|max:255',
            'period_days' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($period->update($request->all())) {
            return $this->sendSuccess("Period Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Period');
        }
    }

    public function destroy(Request $request, $id)
    {
        $period = Period::find($id);

        if (!$period) {
            return $this->sendError('Period Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $period->status = 0;
        $period->deleted_by = $request->deleted_by;
        $period->save();
        if ($period->delete()) {
            return $this->sendSuccess('Period Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Period');
        }
    }
}
