<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Distributor;


class DistributorController extends BaseController
{
    public function index()
    {
        $distributors = Distributor::all();

        if ($distributors->isEmpty()) {
            return $this->sendError('No Distributors Found');
        }

        return $this->sendSuccess($distributors);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'distributor_company' => 'required|max:255',
            'distributor_name' => 'required|max:255',
            'distributor_email' => 'required|max:255',
            'distributor_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $distributor = new Distributor($request->all());
        if ($distributor->save()) {
            return $this->sendSuccess("Distributor Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Distributor');
        }
    }

    public function show($id)
    {
        $distributor = Distributor::find($id);

        if (!$distributor) {
            return $this->sendError('Distributor Not Found');
        }

        return $this->sendSuccess($distributor);
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::find($id);

        if (!$distributor) {
            return $this->sendError('Distributor Not Found');
        }

        $validator = Validator::make($request->all(), [
            'distributor_company' => 'required|max:255',
            'distributor_name' => 'required|max:255',
            'distributor_email' => 'required|max:255',
            'distributor_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($distributor->update($request->all())) {
            return $this->sendSuccess("Distributor Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Distributor');
        }
    }

    public function destroy(Request $request, $id)
    {
        $distributor = Distributor::find($id);

        if (!$distributor) {
            return $this->sendError('Distributor Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $distributor->status = 0;
        $distributor->deleted_by = $request->deleted_by;
        $distributor->save();
        if ($distributor->delete()) {
            return $this->sendSuccess('Distributor Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Distributor');
        }
    }
}
