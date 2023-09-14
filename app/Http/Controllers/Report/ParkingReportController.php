<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingReportController extends Controller
{
    public function get_parking_report(Request $request)
    {
        $parking_duration = $request->input('duration');

        $parkingReports = DB::table('parking_reports as A')
            ->join('vehicles as B', 'A.device_imei', '=', 'B.device_imei')
            ->whereRaw("DATE_ADD(A.start_datetime, INTERVAL 330 MINUTE) >= ?", [$request->input('start_day')])
            ->whereRaw("DATE_ADD(A.end_datetime, INTERVAL 330 MINUTE) <= ?", [$request->input('end_day')])
            ->when($request->input('device_imei') !== 'All', function ($query) use ($request) {
                return $query->where('A.device_imei', '=', $request->input('device_imei'));
            })
            ->whereRaw("TIMESTAMPDIFF(SECOND, A.start_datetime, A.end_datetime) >= ?", [$parking_duration * 60]) // Filter by parking duration in seconds
            ->select(
                'A.id',
                'A.vehicle_id',
                'A.device_imei',
                'B.vehicle_name',
                'A.start_latitude',
                'A.start_longitude',
                DB::raw("DATE_ADD(A.start_datetime, INTERVAL '330' MINUTE) as start_datetime"),
                DB::raw("DATE_ADD(A.end_datetime, INTERVAL 330 MINUTE) as end_datetime"),
                DB::raw("TIME_FORMAT(TIMEDIFF(A.end_datetime, A.start_datetime), '%H:%i:%s') as parking_duration")
            )
            ->orderBy('A.id', 'desc')
            ->get();

        if ($parkingReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Parking Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $parkingReports, "status_code" => 200];
        return response($response, 200);
    }
}
