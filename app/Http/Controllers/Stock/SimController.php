<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Sim;
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
                'sim_imei_no' => 'required|unique:sims,sim_imei_no'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

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
                'sim_id' => 'required|max:255',
                'role_id' => 'required|max:255',
                'user_id' => 'required|max:255'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $role_id = $request->input('role_id');
            $sim_id = $request->input('sim_id');

            $admin_id = $request->input('admin_id');
            $distributor_id = $request->input('distributor_id');
            $dealer_id = $request->input('dealer_id');
            $subdealer_id = $request->input('subdealer_id');

            switch ($role_id) {
                case $role_id == 1:
                    $admin_id = $request->input('user_id');
                    $sim_data =  Sim::where('admin_id', null)
                        ->where('distributor_id', null)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('id', $sim_id)
                        ->update([
                            'admin_id' => $admin_id
                        ]);
                    break;
                case $role_id == 2:
                    $distributor_id = $request->input('user_id');
                    $sim_data =  Sim::where('admin_id', $admin_id)
                        ->where('distributor_id', null)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('id', $sim_id)
                        ->update([
                            'distributor_id' => $distributor_id
                        ]);
                    break;
                case $role_id == 3:
                    $dealer_id = $request->input('user_id');
                    $sim_data = Sim::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('id', $sim_id)
                        ->update([
                            'dealer_id' => $dealer_id
                        ]);
                    break;
                case $role_id == 4:
                    $subdealer_id = $request->input('user_id');
                    $sim_data = Sim::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', $dealer_id)
                        ->where('subdealer_id', null)
                        ->where('id', $sim_id)
                        ->update([
                            'subdealer_id' => $subdealer_id
                        ]);
                    break;
                default:
            }
            if ($sim_data) {
                $response = ["success" => true, "message" => "Sim Transferred Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Transfer Sim", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function sim_list(Request $request)
    {
        try {

            $admin_id = $request->input('admin_id');
            $distributor_id = $request->input('distributor_id');
            $dealer_id = $request->input('dealer_id');
            $subdealer_id = $request->input('subdealer_id');

            $sim_data = DB::table('sims')
                ->select('id', 'sim_imei_no', 'sim_mob_no1', 'sim_mob_no2')
                ->where('admin_id', $admin_id)
                ->where('distributor_id', $distributor_id)
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', $subdealer_id)
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

    public function update(Request $request, $id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        $validator = Validator::make($request->all(), [
            'network_id' => 'required|max:255',
            'sim_imei_no' => 'required|max:255',
            'sim_mob_no' => 'required|max:255',
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
}
