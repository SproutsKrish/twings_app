<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\GeofenceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeofenceReportController extends BaseController
{
    public function geofence_report(Request $request)
    {
        $geofence_reports = GeofenceReport::select('*')
            ->from('geofence_report as A')
            ->join('assign_geofence as B', 'A.assign_geofence_id', '=', 'B.id')
            ->join('geofence as C', 'B.geofence_id', '=', 'C.id')
            ->join('vehicles as D', 'A.vehicle_id', '=', 'D.id')
            ->whereBetween('A.created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('A.vehicle_id', $request->input('vehicle_id'))
            ->get();

        if ($geofence_reports->isEmpty()) {
            return $this->sendError('No Geofence Report Found');
        }

        return $this->sendSuccess($geofence_reports);
    }
}
