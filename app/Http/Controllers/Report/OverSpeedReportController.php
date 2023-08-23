<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\PlaybackReport;
use Illuminate\Http\Request;

class OverSpeedReportController extends Controller
{
    public function speed_report(Request $request)
    {
        $results = PlaybackReport::where('device_datetime', '>=', $request->input('start_day'))
            ->where('device_datetime', '<=', $request->input('end_day'))
            ->where('device_imei', $request->input('device_imei'))
            ->where('speed', '>=', $request->input('speed'))
            ->select('latitude', 'longitude', 'speed', 'device_datetime')
            ->get();

        if (empty($results)) {
            $response = ["success" => false, "message" => 'No Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response($response, 200);
    }
}
