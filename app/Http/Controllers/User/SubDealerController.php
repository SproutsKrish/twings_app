<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\SubDealer;

class SubDealerController extends BaseController
{
    public function index()
    {
        $subdealers = SubDealer::all();

        if ($subdealers->isEmpty()) {
            return $this->sendError('No SubDealers Found');
        }

        return $this->sendSuccess($subdealers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subdealer_company' => 'required|max:255',
            'subdealer_name' => 'required|max:255',
            'subdealer_email' => 'required|max:255',
            'subdealer_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $subdealer = new SubDealer($request->all());
        if ($subdealer->save()) {
            return $this->sendSuccess("SubDealer Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert SubDealer');
        }
    }

    public function show($id)
    {
        $subdealer = SubDealer::find($id);

        if (!$subdealer) {
            return $this->sendError('SubDealer Not Found');
        }

        return $this->sendSuccess($subdealer);
    }

    public function update(Request $request, $id)
    {
        $subdealer = SubDealer::find($id);

        if (!$subdealer) {
            return $this->sendError('SubDealer Not Found');
        }

        $validator = Validator::make($request->all(), [
            'subdealer_company' => 'required|max:255',
            'subdealer_name' => 'required|max:255',
            'subdealer_email' => 'required|max:255',
            'subdealer_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($subdealer->update($request->all())) {
            return $this->sendSuccess("SubDealer Updated Successfully");
        } else {
            return $this->sendError('Failed to Update SubDealer');
        }
    }

    public function destroy(Request $request, $id)
    {
        $subdealer = SubDealer::find($id);

        if (!$subdealer) {
            return $this->sendError('SubDealer Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $subdealer->status = 0;
        $subdealer->deleted_by = $request->deleted_by;
        $subdealer->save();
        if ($subdealer->delete()) {
            return $this->sendSuccess('SubDealer Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete SubDealer');
        }
    }
}
