<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemperatureReportController extends Controller
{
    public function temperature_report(Request $request)
    {
        $temperatureReports = DB::table('temperature_reports AS A')
            ->join('vehicles AS B', 'A.device_imei', '=', 'B.device_imei')
            ->whereBetween('A.created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('A.device_imei', $request->input('device_imei'))
            ->select('B.vehicle_name', 'A.device_imei', 'A.latitude', 'A.longitude', 'A.ignition_status', 'A.temp_status1', 'A.speed', DB::raw('CAST(A.created_at AS DATE) AS date'))
            ->get();


        if ($temperatureReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Temperature Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $temperatureReports, "status_code" => 200];
        return response($response, 200);
    }
}
