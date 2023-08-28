<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\PlaybackReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverSpeedReportController extends Controller
{
    public function speed_report(Request $request)
    {
        $query = "
        SELECT
            number,
            MIN(latitude) AS s_lat,
            MIN(longitude) AS s_lng,
            MAX(latitude) AS e_lat,
            MAX(longitude) AS e_lng,
            MIN(speed) AS s_speed,
            MAX(speed) AS e_speed,
            MAX(speed) - MIN(speed) AS speed_diff,
            MIN(device_datetime + INTERVAL 330 MINUTE) AS s_device_datetime,
            MAX(device_datetime + INTERVAL 330 MINUTE) AS e_device_datetime,
            SEC_TO_TIME(TIMESTAMPDIFF(SECOND, MIN(device_datetime), MAX(device_datetime))) AS time_diff
        FROM (
            SELECT id, latitude, longitude, device_datetime, speed, odometer, number,
                ROW_NUMBER() OVER (PARTITION BY number ORDER BY id) AS row_num_asc,
                ROW_NUMBER() OVER (PARTITION BY number ORDER BY id DESC) AS row_num_desc
            FROM (
                SELECT
                    id,
                    latitude,
                    longitude,
                    device_datetime,
                    speed,
                    odometer,
                    @number := IF(id = @prev_id + 1, @number, @number + 1) AS number,
                    @prev_id := id
                FROM play_back_histories,
                    (SELECT @number := 1, @prev_id := 0) AS init
                WHERE speed >= 10 and device_imei = '2109120102295' and device_datetime + INTERVAL 330 MINUTE BETWEEN '2023-08-01 00:00:00' and '2023-08-31 23:59:59'
                ORDER BY id
            ) AS numbered_records
        ) AS numbered_rows_with_ranks
        WHERE row_num_asc = 1 OR row_num_desc = 1
        GROUP BY number
        ORDER BY number;
    ";

        $result = DB::select(DB::raw($query));


        if (!$result) {
            $response = ["success" => false, "message" => 'No Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $result, "status_code" => 200];
        return response($response, 200);
    }
}
