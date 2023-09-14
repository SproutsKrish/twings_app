<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeviceController extends BaseController
{
    public function index()
    {
        $devices = Device::all();

        if ($devices->isEmpty()) {
            return $this->sendError('No Devices Found');
        }

        return $this->sendSuccess($devices);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'supplier_id' => 'required|max:255',
                'device_make_id' => 'required',
                'device_model_id' => 'required',
                'device_imei_no' => 'required|unique:devices,device_imei_no'
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

            $device = new Device($request->all());

            if ($device->save()) {
                $response = ["success" => true, "message" => "Device Inserted Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Insert Device", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function device_transfer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $device = Device::find($request->input('id'));

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
            $device = $device->update($data);

            if ($device) {
                $response = ["success" => true, "message" => "Device Transferred Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Failed to Transfer Device", "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {

            return $e->getMessage();

            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function device_list(Request $request)
    {
        try {

            $admin_id = auth()->user()->admin_id;
            $distributor_id = auth()->user()->distributor_id;
            $dealer_id = auth()->user()->dealer_id;
            $subdealer_id = auth()->user()->subdealer_id;

            $device_data = DB::table('devices as a')
                ->select('a.id', 'a.device_imei_no', 'a.ccid', 'a.uid', 'a.device_make_id', 'a.device_model_id', 'a.supplier_id', 'b.device_make', 'c.device_model', 'd.supplier_name')
                ->join('device_makes as b', 'a.device_make_id', '=', 'b.id')
                ->join('device_models as c', 'a.device_model_id', '=', 'c.id')
                ->join('suppliers as d', 'a.supplier_id', '=', 'd.id')
                ->where('a.admin_id', $admin_id)
                ->where('a.distributor_id', $distributor_id)
                ->where('a.dealer_id', $dealer_id)
                ->where('a.subdealer_id', $subdealer_id)
                ->where('a.client_id', null)
                ->where('a.status', '1')
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

    public function device_stock_list(Request $request)
    {
        $user_id = $request->input('user_id');

        $data = User::find($user_id);
        $role_id = $data->role_id;
        $dealer_id = null;
        $subdealer_id = null;
        if ($role_id == 4) {
            $dealer_id = $data->dealer_id;

            $results = DB::table('devices')
                ->select('id', 'device_imei_no')
                ->where('dealer_id', $dealer_id)
                ->where('subdealer_id', $subdealer_id)
                ->where('client_id', null)
                ->where('status', '1')
                ->get();
        } else if ($role_id == 5) {
            $subdealer_id = $data->subdealer_id;

            $results = DB::table('devices')
                ->select('id', 'device_imei_no')
                ->where('subdealer_id', $subdealer_id)
                ->where('client_id', null)
                ->where('status', '1')
                ->get();
        }
        if (empty($results)) {
            $response = ["success" => false, "message" => "No Datas Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $results, "status_code" => 200];
            return response()->json($response, 200);
        }
    }


    public function show($id)
    {
        $device = DB::table('devices as a')
            ->select('a.id', 'a.device_imei_no', 'a.ccid', 'a.uid', 'a.device_make_id', 'a.device_model_id', 'a.supplier_id', 'b.device_make', 'c.device_model', 'd.supplier_name')
            ->join('device_makes as b', 'a.device_make_id', '=', 'b.id')
            ->join('device_models as c', 'a.device_model_id', '=', 'c.id')
            ->join('suppliers as d', 'a.supplier_id', '=', 'd.id')
            ->where('a.id', $id)
            ->first();

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        return $this->sendSuccess($device);
    }

    public function update(Request $request)
    {
        $device = Device::find($request->input('id'));

        if (!$device) {
            $response = ["success" => false, "message" => "Device Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'device_imei_no' => 'required|unique:devices,device_imei_no,' . $request->input('id') . 'id',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($device->update($request->all())) {
            $response = ["success" => true, "message" => "Device Updated Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Failed to Update Device", "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function destroy(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $device->status = 0;
        $device->deleted_by = $request->deleted_by;
        $device->save();
        if ($device->delete()) {
            return $this->sendSuccess('Device Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Device');
        }
    }

    public function device_assign(Request $request, $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        if ($device->update($request->all())) {
            return $this->sendSuccess("Device Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device');
        }
    }
}
