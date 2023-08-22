<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DistanceReport;
use Illuminate\Support\Facades\DB;

class DistanceReportController extends Controller
{
    public function get_distance_report(Request $request)
    {
        $startDay = $request->input('start_day');
        $endDay = $request->input('end_day');
        $deviceImei = $request->input('device_imei');

        $results = DB::select("
        SELECT
        temp.date,
        temp.min_datetime AS start_date,
        start_location.latitude AS start_latitude,
        start_location.longitude AS start_longitude,
        temp.min_odometer AS start_odometer,
        temp.max_datetime AS end_date,
        end_location.latitude AS end_latitude,
        end_location.longitude AS end_longitude,
        temp.max_odometer AS end_odometer,
        temp.max_odometer - temp.min_odometer AS odometer_difference
    FROM (
        SELECT
            DATE(device_datetime) AS date,
            MAX(device_datetime) AS max_datetime,
            MAX(odometer) AS max_odometer,
            MIN(device_datetime) AS min_datetime,
            MIN(odometer) AS min_odometer
        FROM play_back_histories
        WHERE device_datetime BETWEEN '$startDay' AND '$endDay'
            AND device_imei = '$deviceImei'
        GROUP BY DATE(device_datetime)
    ) AS temp
    JOIN play_back_histories AS start_location ON start_location.device_datetime = temp.min_datetime
    JOIN play_back_histories AS end_location ON end_location.device_datetime = temp.max_datetime;


    ");

        // dd($results);

        if (empty($results)) {
            $response = ["success" => false, "message" => 'No Distance Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response()->json($response, 200);
    }
}
