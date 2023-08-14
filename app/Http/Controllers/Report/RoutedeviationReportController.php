<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\RoutedeviationReport;
use Illuminate\Http\Request;

class RoutedeviationReportController extends Controller
{
    public function route_deviation_report(Request $request)
    {
        $playbackReports = RoutedeviationReport::whereBetween('created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('device_imei', $request->input('device_imei'))
            ->get();

        if ($playbackReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Route Deviation Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $playbackReports, "status_code" => 200];
        return response()->json($response, 200);
    }
}
