<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeviceController extends BaseController
{
    // public function index()
    // {
    //     $devices = Device::all();

    //     if ($devices->isEmpty()) {
    //         return $this->sendError('No Devices Found');
    //     }

    //     return $this->sendSuccess($devices);
    // }

    //Stock Management Device Add
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $data = $request->only([
                'supplier_id', 'device_make_id', 'device_model_id', 'device_imei_no',
                'ccid', 'uid', 'description'
            ]);

            $validator = Validator::make($request->all(), Device::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->errors(),
                    "status_code" => 403
                ], 403);
            }

            $device = new Device(array_merge($data, [
                'purchase_date' => now(),
                'admin_id' => $user->admin_id,
                'distributor_id' => $user->distributor_id,
                'dealer_id' => $user->dealer_id,
                'subdealer_id' => $user->subdealer_id,
                'created_by' => $user->id,
            ]));

            if ($device->save()) {
                return response()->json([
                    "success" => true,
                    "message" => "Device Inserted Successfully",
                    "status_code" => 200
                ], 200);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Failed to Insert Device",
                    "status_code" => 404
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage(), "status_code" => 404], 404);
        }
    }

    //Vehicle Management Device Add
    public function device_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), Device::validationRules());

            if ($validator->fails()) {
                return response()->json(["success" => false, "message" => $validator->errors(), "status_code" => 403], 403);
            }

            $data = $request->only(['supplier_id', 'device_make_id', 'device_model_id', 'device_imei_no', 'ccid', 'uid', 'description', 'admin_id']);

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

            $device = Device::create($data);

            return response()->json(["success" => true, "message" => "Device Inserted Successfully", "status_code" => 200], 200);
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => $e->getMessage(), "status_code" => 404], 404);
        }
    }

    //Stock Management Device Transfer
    public function device_transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors(), "status_code" => 403], 403);
        }

        try {
            $device = Device::find($request->input('id'));

            if ($device) {
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

                $device->update($data);

                $response = ["success" => true, "message" => "Device Transferred Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Device Not Found", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 500];
            return response()->json($response, 500);
        }
    }

    //Device Stock List in Stock Management
    public function device_list(Request $request)
    {
        try {
            $admin_id = auth()->user()->admin_id;
            $distributor_id = auth()->user()->distributor_id;
            $dealer_id = auth()->user()->dealer_id;
            $subdealer_id = auth()->user()->subdealer_id;

            $device_data = Device::select('devices.id', 'devices.device_imei_no', 'devices.ccid', 'devices.uid', 'devices.device_make_id', 'devices.device_model_id', 'devices.supplier_id', 'device_makes.device_make', 'device_models.device_model', 'suppliers.supplier_name', 'devices.description')
                ->join('device_makes', 'devices.device_make_id', '=', 'device_makes.id')
                ->join('device_models', 'devices.device_model_id', '=', 'device_models.id')
                ->join('suppliers', 'devices.supplier_id', '=', 'suppliers.id')
                ->where('devices.admin_id', $admin_id)
                ->where('devices.distributor_id', $distributor_id)
                ->where('devices.dealer_id', $dealer_id)
                ->where('devices.subdealer_id', $subdealer_id)
                ->whereNull('devices.client_id')
                ->where('devices.status', '1')
                ->orderBy('devices.id', 'desc')
                ->get();

            if ($device_data->isEmpty()) {
                $response = ["success" => false, "message" => "No Device Found", "status_code" => 404];
                return response()->json($response, 404);
            } else {
                $response = ["success" => true, "data" => $device_data, "status_code" => 200];
                return response()->json($response, 200);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    //Device Stock List in Vehicle Management [Dealer/SubDealer]
    public function device_stock_list(Request $request)
    {
        $user_id = $request->input('user_id');

        $user = User::find($user_id);
        $results = Device::availableForUser($user)->select('id', 'device_imei_no')->get();

        if ($results->isEmpty()) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
        } else {
            $response = ["success" => true, "data" => $results, "status_code" => 200];
        }

        return response()->json($response, $response['status_code']);
    }

    //Vehicle Management Device Show
    public function show($id)
    {
        $device = Device::select('devices.id', 'devices.device_imei_no', 'devices.ccid', 'devices.uid', 'devices.device_make_id', 'devices.device_model_id', 'devices.supplier_id', 'device_makes.device_make', 'device_models.device_model', 'suppliers.supplier_name')
            ->join('device_makes', 'devices.device_make_id', '=', 'device_makes.id')
            ->join('device_models', 'devices.device_model_id', '=', 'device_models.id')
            ->join('suppliers', 'devices.supplier_id', '=', 'suppliers.id')
            ->where('devices.id', $id)
            ->first();

        if (!$device) {
            return response()->json(["success" => false, "message" => "Device Not Found", "status_code" => 404], 404);
        }

        $response = ["success" => true, "data" => $device, "status_code" => 200];
        return response()->json($response, $response['status_code']);
    }

    //Stock Management Device Edit
    public function update(Request $request)
    {
        $device = Device::find($request->id);

        if (!$device) {
            return response()->json(["success" => false, "message" => "D Not Found", "status_code" => 404], 404);
        }

        $validator = Validator::make($request->all(), Device::validationRules($request->id));

        if ($validator->fails()) {
            return response()->json(["success" => false, "message" => $validator->errors(), "status_code" => 403], 403);
        }

        if ($device->update($request->all())) {
            return response()->json(["success" => true, "message" => "Device Updated Successfully", "status_code" => 200], 200);
        } else {
            return response()->json(["success" => false, "message" => "Failed to Update Device", "status_code" => 404], 404);
        }
    }

    //Stock Management Device Delete
    public function destroy(Request $request)
    {
        $device = Device::find($request->input('id'));

        if (!$device) {
            $response = ["success" => false, "message" => "Device Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        // $device->status = 0;
        // $device->deleted_by = $request->input('user_id');
        // $device->save();
        if ($device->delete()) {
            $response = ["success" => true, "message" => "Device Deleted Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Failed To Delete Device", "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
