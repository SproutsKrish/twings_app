<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Dealer;

class DealerController extends BaseController
{
    public function index()
    {
        $dealers = Dealer::all();

        if ($dealers->isEmpty()) {
            return $this->sendError('No Dealers Found');
        }

        return $this->sendSuccess($dealers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dealer_company' => 'required|max:255',
            'dealer_name' => 'required|max:255',
            'dealer_email' => 'required|max:255',
            'dealer_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $dealer = new Dealer($request->all());
        if ($dealer->save()) {
            return $this->sendSuccess("Dealer Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Dealer');
        }
    }

    public function show($id)
    {
        $dealer = Dealer::find($id);

        if (!$dealer) {
            return $this->sendError('Dealer Not Found');
        }

        return $this->sendSuccess($dealer);
    }

    public function update(Request $request, $id)
    {
        $dealer = Dealer::find($id);

        if (!$dealer) {
            return $this->sendError('Dealer Not Found');
        }

        $validator = Validator::make($request->all(), [
            'dealer_company' => 'required|max:255',
            'dealer_name' => 'required|max:255',
            'dealer_email' => 'required|max:255',
            'dealer_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($dealer->update($request->all())) {
            return $this->sendSuccess("Dealer Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Dealer');
        }
    }

    public function destroy(Request $request, $id)
    {
        $dealer = Dealer::find($id);

        if (!$dealer) {
            return $this->sendError('Dealer Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $dealer->status = 0;
        $dealer->deleted_by = $request->deleted_by;
        $dealer->save();
        if ($dealer->delete()) {
            return $this->sendSuccess('Dealer Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Dealer');
        }
    }
}
