<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Sim;
use App\Models\User;

class SimController extends BaseController
{
    // public function index()
    // {
    //     $sims = Sim::all();

    //     if ($sims->isEmpty()) {
    //         return $this->sendError('No Sims Found');
    //     }

    //     return $this->sendSuccess($sims);
    // }

    //Stock Management Sim Add
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $data = $request->only([
                'network_id', 'sim_imei_no', 'sim_mob_no1', 'sim_mob_no2',
                'valid_from', 'valid_to'
            ]);

            $validator = Validator::make($data, Sim::validationRules($request->id));

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->errors(),
                    "status_code" => 403
                ], 403);
            }

            $sim = new Sim(array_merge($data, [
                'purchase_date' => now(),
                'admin_id' => $user->admin_id,
                'distributor_id' => $user->distributor_id,
                'dealer_id' => $user->dealer_id,
                'subdealer_id' => $user->subdealer_id,
                'created_by' => $user->id,
            ]));

            if ($sim->save()) {
                return response()->json([
                    "success" => true,
                    "message" => "Sim Inserted Successfully",
                    "status_code" => 200
                ], 200);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Failed to Insert Sim",
                    "status_code" => 404
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
                "status_code" => 404
            ], 404);
        }
    }

    //Vehicle Management Sim Add
    public function sim_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), Sim::validationRules());

            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors(), "status_code" => 403], 403);
            }

            $data = $request->only(['network_id', 'sim_imei_no', 'sim_mob_no1', 'sim_mob_no2', 'admin_id']);
            $data['purchase_date'] = now(); // Current date and time

            $distributor = User::find($request->input('distributor_id'));
            $data['distributor_id'] = $distributor->distributor_id;

            $dealer = User::find($request->input('dealer_id'));
            $data['dealer_id'] = $dealer->dealer_id;

            $subdealer_id = $request->input('subdealer_id');
            if ($subdealer_id) {
                $subdealer = User::find($subdealer_id);
                $data['subdealer_id'] = $subdealer->subdealer_id;
            }

            $data['created_by'] = auth()->user()->id;

            $sim = Sim::create($data);

            return response()->json(["success" => true, "message" => "Sim Inserted Successfully", "status_code" => 200], 200);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage(), "status_code" => 404], 404);
        }
    }

    //Stock Management Sim Transfer
    public function sim_transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors(), "status_code" => 403], 403);
        }

        try {
            $sim = Sim::find($request->input('id'));

            if ($sim) {
                $data = [];

                if ($request->has('admin_id')) {
                    $admin = User::find($request->input('admin_id'));
                    $data['admin_id'] = $admin->admin_id;
                }
                if ($request->has('distributor_id')) {
                    $distributor = User::find($request->input('distributor_id'));
                    $data['distributor_id'] = $distributor->distributor_id;
                }
                if ($request->has('dealer_id')) {
                    $dealer = User::find($request->input('dealer_id'));
                    $data['dealer_id'] = $dealer->dealer_id;
                }
                if ($request->has('subdealer_id')) {
                    $subdealer = User::find($request->input('subdealer_id'));
                    $data['subdealer_id'] = $subdealer->subdealer_id;
                }

                $sim->update($data);

                $response = ["success" => true, "message" => "Sim Transferred Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Sim Not Found", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 500];
            return response()->json($response, 500);
        }
    }

    //Sim Stock List in Stock Management
    public function sim_list(Request $request)
    {
        try {
            $admin_id = auth()->user()->admin_id;
            $distributor_id = auth()->user()->distributor_id;
            $dealer_id = auth()->user()->dealer_id;
            $subdealer_id = auth()->user()->subdealer_id;

            $sim_data = Sim::select('sims.id', 'sims.network_id', 'sims.sim_imei_no', 'sims.sim_mob_no1', 'sims.sim_mob_no2', 'sims.valid_from', 'sims.valid_to', 'network_providers.network_provider_name')
                ->join('network_providers', 'sims.network_id', '=', 'network_providers.id')
                ->where('sims.admin_id', $admin_id)
                ->where('sims.distributor_id', $distributor_id)
                ->where('sims.dealer_id', $dealer_id)
                ->where('sims.subdealer_id', $subdealer_id)
                ->whereNull('sims.client_id')
                ->where('sims.status', '1')
                ->orderBy('sims.id', 'desc')
                ->get();

            if ($sim_data->isEmpty()) {
                $response = ["success" => false, "message" => "No Sims Found", "status_code" => 404];
                return response()->json($response, 404);
            } else {
                $response = ["success" => true, "data" => $sim_data, "status_code" => 200];
                return response()->json($response, 200);
            }
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    //Sim Stock List in Vehicle Management [Dealer/SubDealer]
    public function sim_stock_list(Request $request)
    {
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $results = Sim::availableForUser($user)->select('id', 'sim_mob_no1')->get();

        if ($results->isEmpty()) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
        } else {
            $response = ["success" => true, "data" => $results, "status_code" => 200];
        }

        return response()->json($response, $response['status_code']);
    }

    //Vehicle Management Sim Show
    public function show($id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return response()->json(["success" => false, "message" => "Sim Not Found", "status_code" => 404], 404);
        }

        $response = ["success" => true, "data" => $sim, "status_code" => 200];
        return response()->json($response, $response['status_code']);
    }

    //Stock Management Sim Edit
    public function update(Request $request)
    {
        $sim = Sim::find($request->id);

        if (!$sim) {
            return response()->json(["success" => false, "message" => "Sim Not Found", "status_code" => 404], 404);
        }

        $validator = Validator::make($request->all(), Sim::validationRules($request->id));

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors(), "status_code" => 403], 403);
        }

        if ($sim->update($request->all())) {
            return response()->json(["success" => true, "message" => "Sim Updated Successfully", "status_code" => 200], 200);
        } else {
            return response()->json(["success" => false, "message" => "Failed to Update Sim", "status_code" => 404], 404);
        }
    }

    //Stock Management Sim Delete
    public function destroy(Request $request)
    {
        $sim = Sim::find($request->input('id'));

        if (!$sim) {
            $response = ["success" => false, "message" => "Sim Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        // $sim->status = 0;
        // $sim->deleted_by = $request->input('user_id');
        // $sim->save();
        if ($sim->delete()) {
            $response = ["success" => true, "message" => "Sim Deleted Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Failed To Delete Sim", "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
