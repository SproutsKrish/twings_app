<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class IdleReportController extends Controller
{
    public function get_idle_report(Request $request)
    {
        $idleReports = DB::table('idle_reports as A')
            ->join('vehicles as B', 'A.vehicle_id', '=', 'B.id')
            ->where('A.start_datetime', '>=', $request->input('start_day'))
            ->where('A.end_datetime', '<=', $request->input('end_day'))
            ->where('A.vehicle_id', '=', $request->input('vehicle_id'))
            ->select('A.*', 'B.vehicle_make')
            ->get();

        if ($idleReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Idle Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $idleReports, "status_code" => 200];
        return response($response, 200);
    }
}
