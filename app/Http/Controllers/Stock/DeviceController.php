<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Device;
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
                'device_id' => 'required|max:255',
                'role_id' => 'required|max:255',
                'user_id' => 'required|max:255'
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
                return response()->json($response, 403);
            }

            $role_id = $request->input('role_id');
            $device_id = $request->input('device_id');

            $admin_id = $request->input('admin_id');
            $distributor_id = $request->input('distributor_id');
            $dealer_id = $request->input('dealer_id');
            $subdealer_id = $request->input('subdealer_id');

            switch ($role_id) {
                case $role_id == 1:
                    $admin_id = $request->input('user_id');
                    $device_data =  Device::where('admin_id', null)
                        ->where('distributor_id', null)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('id', $device_id)
                        ->update([
                            'admin_id' => $admin_id
                        ]);
                    break;
                case $role_id == 2:
                    $distributor_id = $request->input('user_id');
                    $device_data =  Device::where('admin_id', $admin_id)
                        ->where('distributor_id', null)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('id', $device_id)
                        ->update([
                            'distributor_id' => $distributor_id
                        ]);
                    break;
                case $role_id == 3:
                    $dealer_id = $request->input('user_id');
                    $device_data = Device::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', null)
                        ->where('subdealer_id', null)
                        ->where('id', $device_id)
                        ->update([
                            'dealer_id' => $dealer_id
                        ]);
                    break;
                case $role_id == 4:
                    $subdealer_id = $request->input('user_id');
                    $device_data = Device::where('admin_id', $admin_id)
                        ->where('distributor_id', $distributor_id)
                        ->where('dealer_id', $dealer_id)
                        ->where('subdealer_id', null)
                        ->where('id', $device_id)
                        ->update([
                            'subdealer_id' => $subdealer_id
                        ]);
                    break;
                default:
            }
            if ($device_data) {
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


    public function show($id)
    {
        $device = Device::find($id);

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        return $this->sendSuccess($device);
    }

    public function update(Request $request)
    {
        $device = Device::find($request->input('id'));

        if (!$device) {
            return $this->sendError('Device Not Found');
        }

        $validator = Validator::make($request->all(), [
            'device_imei_no' => 'required|unique:devices,device_imei_no,' . $request->input('id') . 'id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($device->update($request->all())) {
            return $this->sendSuccess("Device Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Device');
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
