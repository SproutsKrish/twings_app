<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Client;

class ClientController extends BaseController
{
    public function index()
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            return $this->sendError('No Clients Found');
        }

        return $this->sendSuccess($clients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_company' => 'required|max:255',
            'client_name' => 'required|max:255',
            'client_email' => 'required|max:255',
            'client_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $client = new Client($request->all());
        if ($client->save()) {
            return $this->sendSuccess("Client Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Client');
        }
    }

    public function show($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->sendError('Client Not Found');
        }

        return $this->sendSuccess($client);
    }

    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->sendError('Client Not Found');
        }

        $validator = Validator::make($request->all(), [
            'client_company' => 'required|max:255',
            'client_name' => 'required|max:255',
            'client_email' => 'required|max:255',
            'client_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($client->update($request->all())) {
            return $this->sendSuccess("Client Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Client');
        }
    }

    public function destroy(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->sendError('Client Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $client->status = 0;
        $client->deleted_by = $request->deleted_by;
        $client->save();
        if ($client->delete()) {
            return $this->sendSuccess('Client Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Client');
        }
    }

    public function link_generate($id)
    {
        $link  = base_path();

        return $this->sendSuccess($link);
    }
}
