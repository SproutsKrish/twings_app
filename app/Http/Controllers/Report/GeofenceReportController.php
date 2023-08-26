<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GeofenceReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GeofenceReportController extends Controller
{
    public function geofence_report(Request $request)
    {
        $geofence_reports = GeofenceReport::select(
            'A.id',
            'A.vehicle_id',
            'A.device_imei',
            'D.vehicle_name',
            'C.location_short_name',
            'A.out_latitude',
            'A.out_longitude',
            DB::raw("DATE_ADD(A.out_datetime, INTERVAL '330' MINUTE) as out_datetime"),
            'A.in_latitude',
            'A.in_longitude',
            DB::raw("DATE_ADD(A.in_datetime, INTERVAL 330 MINUTE) as in_datetime"),
            'A.distance'
        )
            ->from('geofence_reports as A')
            ->join('assign_geofences as B', 'A.assign_geofence_id', '=', 'B.id')
            ->join('geofences as C', 'B.geofence_id', '=', 'C.id')
            ->join('vehicles as D', 'A.device_imei', '=', 'D.device_imei')
            ->when($request->input('device_imei') !== 'All', function ($query) use ($request) {
                return $query->where('A.device_imei', '=', $request->input('device_imei'));
            })
            ->whereBetween(DB::raw('DATE_ADD(A.created_at, INTERVAL 330 MINUTE)'), [
                Carbon::parse($request->input('start_day')),  // Adjusted start time
                Carbon::parse($request->input('end_day'))     // Adjusted end time
            ])
            ->get();


        if ($geofence_reports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Geofence Report Found', "status_code" => 404];
            return response()->json($response, 404);
        }
        $response = ["success" => true, "data" => $geofence_reports, "status_code" => 200];
        return response()->json($response, 200);
    }
}
