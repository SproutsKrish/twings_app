<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemperatureReportController extends Controller
{
    public function temperature_report(Request $request)
    {
        $temperatureReports = DB::table('temperature_reports as A')
            ->join('vehicles as B', 'A.device_imei', '=', 'B.device_imei')
            ->join('configurations as C', 'C.vehicle_id', '=', 'B.id')
            ->whereBetween('A.created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('A.device_imei', $request->input('device_imei'))
            ->select('A.*', 'B.vehicle_name', 'c.temp_low', 'c.temp_high')
            ->get();

        if ($temperatureReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Temperature Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $temperatureReports, "status_code" => 200];
        return response($response, 200);
    }
}
