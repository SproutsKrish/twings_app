<?php

namespace App\Http\Controllers\Report;


use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\AssignGeofence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AssignGeofenceController extends BaseController
{
    public function index()
    {
        $assign_geofences = AssignGeofence::all();

        if ($assign_geofences->isEmpty()) {
            return $this->sendError('No Assigned Geofences Found');
        }

        return $this->sendSuccess($assign_geofences);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|max:255',
            'client_id' => 'required|max:255',
            'geofence_id' => 'required|max:255',
            'fence_type' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $assign_geofence = new AssignGeofence($request->all());
        if ($assign_geofence->save()) {
            return $this->sendSuccess("Geofence Assigned Successfully");
        } else {
            return $this->sendError('Failed to Assign Geofence');
        }
    }

    public function show($id)
    {
        $assign_geofence = AssignGeofence::find($id);

        if (!$assign_geofence) {
            return $this->sendError('Geofence Assign Not Found');
        }

        return $this->sendSuccess($assign_geofence);
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
            return $this->sendSuccess("Geofence Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Geofence');
        }
    }
}
