<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertReportController extends Controller
{
    public function alert_report(Request $request)
    {
        $alertReports = DB::table('alert_reports as A')
            ->join('vehicles as B', 'A.vehicle_id', '=', 'B.id')
            ->whereBetween('A.created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('A.vehicle_id', $request->input('vehicle_id'))
            ->select('A.*', 'B.vehicle_name')
            ->get();

        if ($alertReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Alerts Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $alertReports, "status_code" => 200];
        return response($response, 200);
    }
}
