<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\NetworkProvider;

class NetworkProviderController extends BaseController
{
    public function index()
    {
        $networks = NetworkProvider::all();

        if ($networks->isEmpty()) {
            return $this->sendError('No Network Provider Found');
        }

        return $this->sendSuccess($networks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_provider_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $network = new NetworkProvider($request->all());
        if ($network->save()) {
            return $this->sendSuccess("Network Provider Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Network Provider');
        }
    }

    public function show($id)
    {
        $network = NetworkProvider::find($id);

        if (!$network) {
            return $this->sendError('Network Provider Not Found');
        }

        return $this->sendSuccess($network);
    }

    public function update(Request $request, $id)
    {
        $network = NetworkProvider::find($id);

        if (!$network) {
            return $this->sendError('Network Provider Not Found');
        }

        $validator = Validator::make($request->all(), [
            'network_provider_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($network->update($request->all())) {
            return $this->sendSuccess("Network Provider Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Network Provider');
        }
    }

    public function destroy(Request $request, $id)
    {
        $network = NetworkProvider::find($id);

        if (!$network) {
            return $this->sendError('Network Provider Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $network->status = 0;
        $network->deleted_by = $request->deleted_by;
        $network->save();
        if ($network->delete()) {
            return $this->sendSuccess('Network Provider Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Network Provider');
        }
    }
}
