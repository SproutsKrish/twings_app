<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\AssignGeofence;
use Illuminate\Http\Request;
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
            'geofence_id' => 'required',
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
}
