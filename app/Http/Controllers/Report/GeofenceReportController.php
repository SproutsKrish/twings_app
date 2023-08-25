<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GeofenceReport;
use Illuminate\Http\Request;

class GeofenceReportController extends Controller
{
    public function geofence_report(Request $request)
    {
        $geofence_reports = GeofenceReport::select('D.vehicle_name', 'A.*')
            ->from('geofence_reports as A')
            ->join('assign_geofences as B', 'A.assign_geofence_id', '=', 'B.id')
            ->join('geofences as C', 'B.geofence_id', '=', 'C.id')
            ->join('vehicles as D', 'A.device_imei', '=', 'D.device_imei')
            ->whereBetween('A.created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('A.device_imei', $request->input('device_imei'))
            ->get();

        if ($geofence_reports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Geofence Report Found', "status_code" => 404];
            return response()->json($response, 404);
        }
        $response = ["success" => true, "data" => $geofence_reports, "status_code" => 200];
        return response()->json($response, 200);
    }
}
