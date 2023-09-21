<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Geofence;

class GeofenceController extends Controller
{
    public function index()
    {
        $geofences = Geofence::where('active_code', 1)->get();

        if ($geofences->isEmpty()) {
            $response = ["success" => false, "message" => 'No Geofences Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $geofences, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_short_name' => 'required|unique:geofences,location_short_name',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'radius' => 'required|max:255',
            'client_id' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $geofence = new Geofence($request->all());
        if ($geofence->save()) {
            $response = ["success" => true, "message" => 'Geofence Inserted Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Insert Geofence', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function show($id)
    {
        $geofence = Geofence::find($id);

        if (!$geofence) {
            $response = ["success" => false, "message" => 'Geofence Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }
        $response = ["success" => true, "data" => $geofence, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        $geofence = Geofence::find($id);

        if (!$geofence) {
            $response = ["success" => false, "message" => 'No Geofence Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'location_short_name' => 'required|max:255',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'radius' => 'required|max:255',
            'client_id' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($geofence->update($request->all())) {
            $response = ["success" => true, "message" => 'Geofence Updated Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Update Geofence', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function destroy(Request $request, $id)
    {
        $Geofence = Geofence::find($id);

        if (!$Geofence) {
            $response = ["success" => false, "message" => 'Geofence Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        if ($Geofence->delete()) {
            $response = ["success" => true, "message" => 'Geofence Deleted Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Delete Geofence', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
