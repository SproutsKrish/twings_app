<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcReportController extends Controller
{
    public function ac_report(Request $request)
    {
        $acReports = DB::table('ac_reports as A')
            ->join('vehicles as B', 'A.vehicle_id', '=', 'B.id')
            ->where('A.start_datetime', '>=', $request->input('start_day'))
            ->where('A.end_datetime', '<=', $request->input('end_day'))
            ->where('A.vehicle_id', '=', $request->input('vehicle_id'))
            ->select('A.*', 'B.vehicle_make', DB::raw("TIME_FORMAT(TIMEDIFF(A.end_datetime, A.start_datetime), '%H:%i:%s') as idle_duration"))
            ->get();

        if ($acReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No AC Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $acReports, "status_code" => 200];
        return response($response, 200);
    }
}
