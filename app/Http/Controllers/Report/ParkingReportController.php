<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingReportController extends Controller
{
    public function get_parking_report(Request $request)
    {
        $parkingReports = DB::table('parking_reports as A')
            ->join('vehicles as B', 'A.vehicle_id', '=', 'B.id')
            ->where('A.start_datetime', '>=', $request->input('start_day'))
            ->where('A.end_datetime', '<=', $request->input('end_day'))
            ->where('A.vehicle_id', '=', $request->input('vehicle_id'))
            ->select('A.*', 'B.vehicle_name', DB::raw("TIME_FORMAT(TIMEDIFF(A.end_datetime, A.start_datetime), '%H:%i:%s') as parking_duration"))
            ->get();


        if ($parkingReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Parking Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $parkingReports, "status_code" => 200];
        return response($response, 200);
    }
}
