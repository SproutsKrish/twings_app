<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class KeyOnKeyOffReportController extends Controller
{
    public function get_keyonoff_report(Request $request)
    {
        // $results = DB::select("SELECT dt.start_datetime, dt.start_latitude, dt.start_longitude, dt.end_datetime, dt.end_latitude, dt.end_longitude, MAX(t.speed) AS max_speed, AVG(t.speed) AS avg_speed, MAX(t.odometer) - MIN(t.odometer) AS distance, dt.duration FROM play_back_histories t JOIN ( SELECT TIMEDIFF(end_datetime, start_datetime) as duration, start_datetime, end_datetime, start_latitude, start_longitude, end_latitude, end_longitude FROM keyoff_keyon_reports WHERE start_datetime >= '2023-08-16 06:00:00' AND end_datetime <= '2023-08-17 23:59:59' AND device_imei = '2109120102295' ) dt ON t.device_datetime >= dt.start_datetime AND t.device_datetime <= dt.end_datetime GROUP BY dt.duration, dt.start_datetime, dt.end_datetime ORDER BY dt.start_datetime;");

        $startDay = $request->input('start_day');
        $endDay = $request->input('end_day');
        $deviceIMEI = $request->input('device_imei');

        $results = DB::select("SELECT dt.start_datetime, dt.start_latitude, dt.start_longitude, dt.end_datetime, dt.end_latitude, dt.end_longitude, MAX(t.speed) AS max_speed, AVG(t.speed) AS avg_speed, MAX(t.odometer) - MIN(t.odometer) AS distance, dt.duration
            FROM play_back_histories t
            JOIN (
                SELECT TIMEDIFF(end_datetime, start_datetime) as duration, start_datetime, end_datetime, start_latitude, start_longitude, end_latitude, end_longitude
                FROM keyoff_keyon_reports
                WHERE start_datetime >= '$startDay'
                AND end_datetime <= '$endDay'
                AND device_imei = '$deviceIMEI'
            ) dt ON t.device_datetime >= dt.start_datetime AND t.device_datetime <= dt.end_datetime
            GROUP BY dt.duration, dt.start_datetime, dt.end_datetime
            ORDER BY dt.start_datetime;");

        if (empty($results)) {
            $response = ["success" => false, "message" => 'No KeyOnOff Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response($response, 200);
    }
}
