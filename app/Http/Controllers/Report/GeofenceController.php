<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\Geofence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeofenceController extends BaseController
{
    public function index()
    {
        $geofences = Geofence::all();

        if ($geofences->isEmpty()) {
            return $this->sendError('No Geofence Found');
        }

        return $this->sendSuccess($geofences);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_short_name' => 'required|max:255',
            'lat' => 'required|max:255',
            'lng' => 'required|max:255',
            'circle_size' => 'required|max:255',
            'radius' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $geofence = new Geofence($request->all());
        if ($geofence->save()) {
            return $this->sendSuccess("Geofence Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Geofence');
        }
    }

    public function show($id)
    {
        $geofence = Geofence::find($id);

        if (!$geofence) {
            return $this->sendError('Geofence Not Found');
        }

        return $this->sendSuccess($geofence);
    }

    public function update(Request $request, $id)
    {
        $geofence = Geofence::find($id);

        if (!$geofence) {
            return $this->sendError('Geofence Not Found');
        }

        $validator = Validator::make($request->all(), [
            'location_short_name' => 'required|max:255',
            'lat' => 'required|max:255',
            'lng' => 'required|max:255',
            'circle_size' => 'required|max:255',
            'radius' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($geofence->update($request->all())) {
            return $this->sendSuccess("Geofence Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Geofence');
        }
    }
}
