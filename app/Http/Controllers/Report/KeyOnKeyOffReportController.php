<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class KeyOnKeyOffReportController extends Controller
{
    public function get_keyonoff_report(Request $request)
    {
        $startDay = $request->input('start_day');
        $endDay = $request->input('end_day');
        $deviceIMEI = $request->input('device_imei');

        $result = DB::select("SELECT
        dt.vehicle_id,
        c.vehicle_name AS vehicle_name,
        DATE_ADD(dt.start_datetime) AS start_datetime,
        dt.start_latitude,
        dt.start_longitude,
        DATE_ADD(dt.end_datetime) AS end_datetime,
        dt.end_latitude,
        dt.end_longitude,
        FORMAT(MAX(t.speed), 2) AS max_speed,
        FORMAT(AVG(t.speed), 2) AS avg_speed,
        FORMAT(MAX(t.odometer) - MIN(t.odometer), 2) AS distance,
        dt.duration
    FROM
        (
            SELECT
                vehicle_id,
                start_latitude,
                start_longitude,
                end_latitude,
                end_longitude,
                DATE_ADD(start_datetime, INTERVAL 330 MINUTE) as start_datetime,
                DATE_ADD(end_datetime, INTERVAL 330 MINUTE) as end_datetime,
                TIMEDIFF(end_datetime, start_datetime) AS duration
            FROM
                keyoff_keyon_reports
            WHERE
                DATE_ADD(start_datetime, INTERVAL 330 MINUTE) >= '$startDay'
                AND DATE_ADD(end_datetime, INTERVAL 330 MINUTE) <= '$endDay'
                AND device_imei = '$deviceIMEI'
        ) AS dt
    JOIN
        play_back_histories AS t
        ON DATE_ADD(t.device_datetime, INTERVAL 330 MINUTE) >= dt.start_datetime AND DATE_ADD(t.device_datetime, INTERVAL 330 MINUTE) <= dt.end_datetime
    JOIN
        vehicles AS c
        ON c.id = dt.vehicle_id
    GROUP BY
        dt.vehicle_id,
        dt.duration,
        dt.start_datetime,
        dt.end_datetime,
        dt.start_latitude,
        dt.start_longitude,
        dt.end_latitude,
        dt.end_longitude,
        c.vehicle_name
    ORDER BY
        t.id asc");

        if (empty($result)) {
            $response = ["success" => false, "message" => 'No KeyOnOff Data Found', "status_code" => 404];
            return response($response, 404);
        }

        $response = ["success" => true, "data" => $result, "status_code" => 200];
        return response($response, 200);
    }
}
