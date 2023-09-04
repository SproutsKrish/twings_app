<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Sim;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SimController extends BaseController
{
    public function index()
    {
        $sims = Sim::all();

        if ($sims->isEmpty()) {
            return $this->sendError('No Sims Found');
        }

        return $this->sendSuccess($sims);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'network_id' => 'required|max:255',
                'sim_imei_no' => 'required|unique:sims,sim_imei_no',
                'sim_mob_no1' => 'required|unique:sims,sim_mob_no1'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $request['admin_id'] = auth()->user()->admin_id;
            $request['distributor_id'] = auth()->user()->distributor_id;
            $request['dealer_id'] = auth()->user()->dealer_id;
            $request['subdealer_id'] = auth()->user()->subdealer_id;
            $request['created_by'] = auth()->user()->id;
            $request['purchase_date'] = date('Y-m-d');

            // return response()->json($request->all());


            $sim = new Sim($request->all());

            if ($sim->save()) {
                $response = ["success" => true, "message" => "Sim Inserted Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Insert Sim", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function sim_transfer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $sim = Sim::find($request->input('id'));

            $requestKeys = collect($request->all())->keys();

            if ($requestKeys->contains('admin_id')) {
                $admin_id = User::find($request->input('admin_id'));
                $data['admin_id']  = $admin_id->admin_id;
            }
            if ($requestKeys->contains('distributor_id')) {
                $distributor_id = User::find($request->input('distributor_id'));
                $data['distributor_id']  = $distributor_id->distributor_id;
            }
            if ($requestKeys->contains('dealer_id')) {
                $dealer_id = User::find($request->input('dealer_id'));
                $data['dealer_id']  = $dealer_id->dealer_id;
            }
            if ($requestKeys->contains('subdealer_id')) {
                $subdealer_id = User::find($request->input('subdealer_id'));
                $data['subdealer_id']  = $subdealer_id->subdealer_id;
            }
            $sim = $sim->update($data);
            return response()->json($sim);
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function sim_list(Request $request)
    {
        try {

            $admin_id = auth()->user()->admin_id;
            $distributor_id = auth()->user()->distributor_id;
            $dealer_id = auth()->user()->dealer_id;
            $subdealer_id = auth()->user()->subdealer_id;

            $sim_data = DB::table('sims as a')
                ->join('network_providers as b', 'a.network_id', '=', 'b.id')
                ->select('a.id', 'a.network_id', 'a.sim_imei_no', 'a.sim_mob_no1', 'a.sim_mob_no2', 'a.valid_from', 'a.valid_to', 'b.network_provider_name')
                ->where('a.admin_id', $admin_id)
                ->where('a.distributor_id', $distributor_id)
                ->where('a.dealer_id', $dealer_id)
                ->where('a.subdealer_id', $subdealer_id)
                ->get();

            if ($sim_data->isEmpty()) {
                $response = ["success" => false, "message" => "No Sims Found", "status_code" => 404];
                return response()->json($response, 404);
            } else {
                $response = ["success" => true, "data" => $sim_data, "status_code" => 200];
                return response()->json($response, 200);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function show($id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        return $this->sendSuccess($sim);
    }

    public function update(Request $request)
    {
        $sim = Sim::find($request->id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }


        $validator = Validator::make($request->all(), [
            'network_id' => 'required|max:255',
            'sim_imei_no' => 'required|unique:sims,sim_imei_no,' . $request->input('id') . 'id',
            'sim_mob_no1' => 'required|unique:sims,sim_mob_no1,' . $request->input('id') . 'id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($sim->update($request->all())) {
            return $this->sendSuccess("Sim Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Sim');
        }
    }

    public function destroy(Request $request, $id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $sim->status = 0;
        $sim->deleted_by = $request->deleted_by;
        $sim->save();
        if ($sim->delete()) {
            return $this->sendSuccess('Sim Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Sim');
        }
    }

    public function sim_assign(Request $request, $id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        if ($sim->update($request->all())) {
            return $this->sendSuccess("Sim Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Sim');
        }
    }

    public function sim_new(Request $request)
    {
        $requestKeys = collect($request->all())->keys();
        return response()->json($requestKeys);
    }
}
