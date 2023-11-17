<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\AssignGeofence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AssignGeofenceController extends Controller
{
    public function index()
    {
        $assign_geofences = AssignGeofence::all();

        if ($assign_geofences->isEmpty()) {
            $response = ["success" => false, "message" => 'No Assigned Geofences Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $assign_geofences, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_imei' => 'required',
            'geofence_id' => 'required|unique:assign_geofences,geofence_id,NULL,id,device_imei,' . $request->input('device_imei'),
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $assign_geofence = new AssignGeofence($request->all());

        if ($assign_geofence->save()) {
            $response = ["success" => true, "message" => 'Geofence Assigned Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Assign Geofence', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function assigned_fence_list(Request $request)
    {
        $device_imei = $request->input('device_imei');

        $data = DB::table('assign_geofences as a')
            ->join('geofences as b', 'a.geofence_id', '=', 'b.id')
            ->join('vehicles as c', 'c.device_imei', '=', 'a.device_imei')

            ->where('a.device_imei', $device_imei)
            ->select('a.id', 'c.vehicle_name', 'b.location_short_name', 'b.latitude', 'b.longitude', 'b.circle_size', 'b.radius', 'a.device_imei')
            ->get();

        if ($data->isEmpty()) {
            $response = ["success" => false, "message" => 'Geofence Assigned List Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }
        $response = ["success" => true, "data" => $data, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function show($id)
    {
        $assign_geofence = AssignGeofence::find($id);

        if (!$assign_geofence) {
            $response = ["success" => false, "message" => 'Geofence Assign Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $assign_geofence, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        $assign_geofence = AssignGeofence::find($id);

        if (!$assign_geofence) {
            return $this->sendError('Geofence Assign Not Found');
        }

        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|max:255',
            'client_id' => 'required|max:255',
            'geofence_id' => 'required|max:255',
            'fence_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($assign_geofence->update($request->all())) {
            $response = ["success" => true, "message" => 'Geofence Assigned Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Assign Geofence', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function destroy(Request $request, $id)
    {
        $data = AssignGeofence::find($id);
        if (!$data) {
            $response = ["success" => false, "message" => 'Data Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        if ($data->delete()) {
            $response = ["success" => false, "message" => 'Data Deleted Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Data Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function assign_geofencelist($geofenceId)
    {
        $data = DB::table('assign_geofences as a')
            ->join('geofences as b', 'a.geofence_id', '=', 'b.id')
            ->join('vehicles as c', 'c.device_imei', '=', 'a.device_imei')
            ->where('b.id', $geofenceId)
            ->select('a.id', 'c.vehicle_name', 'b.location_short_name', 'b.latitude', 'b.longitude', 'b.circle_size', 'b.radius', 'a.device_imei')
            ->get();

        if ($data->isEmpty()) {
            $response = ["success" => false, "message" => 'Geofence not assigned to any vehicles', "status_code" => 404];
            return response()->json($response, 404);
        }
        $response = ["success" => true, "data" => $data, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function geofence_not_assign_vehicles()
    {
        $results = DB::table('assign_geofences')
            ->select('device_imei')
            ->distinct()
            ->get();

        $imeiValues = $results->pluck('device_imei')->toArray();

        $response = DB::table('vehicles as v')
            ->join('assign_geofences as ass_geo', 'ass_geo.device_imei', '=', 'v.device_imei')
            ->where('status', 1)
            ->whereNotIn('v.device_imei', $imeiValues)
            ->select('ass_geo.id', 'v.device_imei', 'v.vehicle_name')
            ->get();

        if ($response->isEmpty()) {
            $response = ["success" => false, "message" => $response, "status_code" => 200];
            return response()->json($response, 200);
        }
        $response = ["success" => true, "data" => $response, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function geofence_assign_vehicle(Request $request)
    {
        $geofence_id = $request->input('geofence_id');

        $results = DB::table('assign_geofences')
            ->where('geofence_id', $geofence_id)
            ->select('device_imei')
            ->distinct()
            ->get();

        $imeiValues = $results->pluck('device_imei')->toArray();

        $response = DB::table('vehicles as v')
            ->join('assign_geofences as ass_geo', 'ass_geo.device_imei', '=', 'v.device_imei')
            ->where('status', 1)
            ->whereIn('v.device_imei', $imeiValues)
            ->select('ass_geo.id', 'v.device_imei', 'v.vehicle_name')
            ->get();

        if ($response->isEmpty()) {
            $response = ["success" => false, "message" => $response, "status_code" => 200];
            return response()->json($response, 200);
        }
        $response = ["success" => true, "data" => $response, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function geofence_not_assign_vehicle(Request $request)
    {
        $geofence_id = $request->input('geofence_id');

        $results = DB::table('assign_geofences')
            ->where('geofence_id', $geofence_id)
            ->select('device_imei')
            ->distinct()
            ->get();

        $imeiValues = $results->pluck('device_imei')->toArray();

        $response = DB::table('vehicles')
            ->where('status', 1)
            ->whereNotIn('device_imei', $imeiValues)
            ->select('id', 'device_imei', 'vehicle_name')
            ->get();

        if ($response->isEmpty()) {
            $response = ["success" => false, "message" => $response, "status_code" => 200];
            return response()->json($response, 200);
        }
        $response = ["success" => true, "data" => $response, "status_code" => 200];
        return response()->json($response, 200);
    }
}
