<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DistanceReport;


class DistanceReportController extends Controller
{
    public function get_distance_report(Request $request)
    {
        $distance_reports =  DistanceReport::whereBetween('date', [$request->input('start_day'), $request->input('end_day')])
            ->where('vehicle_id', $request->input('vehicle_id'))
            ->get();


        if ($distance_reports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Distance Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $distance_reports, "status_code" => 200];
        return response()->json($response, 200);
    }
}
