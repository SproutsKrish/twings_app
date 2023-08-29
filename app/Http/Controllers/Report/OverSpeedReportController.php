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
        $start_day =  $request->input('start_day');
        $end_day =  $request->input('end_day');
        $device_imei = $request->input('device_imei');
        $speed = $request->input('speed');

        $query = "
        SELECT
            number,
            MIN(latitude) AS start_latitude,
            MIN(longitude) AS start_longitude,
            MAX(latitude) AS end_latitude,
            MAX(longitude) AS end_longitude,
            MIN(speed) AS start_speed,
            MAX(speed) AS end_speed,
            MIN(odometer) AS start_odometer,
            MAX(odometer) AS end_odometer,
            ROUND(MAX(odometer) - MIN(odometer), 2) AS odometer_difference,
            MIN(device_datetime + INTERVAL 330 MINUTE) AS start_device_datetime,
            MAX(device_datetime + INTERVAL 330 MINUTE) AS end_device_datetime,
            SEC_TO_TIME(TIMESTAMPDIFF(SECOND, MIN(device_datetime), MAX(device_datetime))) AS time_difference
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
                WHERE speed >= $speed and device_imei = '$device_imei' and device_datetime + INTERVAL 330 MINUTE BETWEEN '$start_day' and '$end_day'
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

    public function demo_app()
    {
        $vehicles = DB::table('vehicles as a')
            ->join('twings_api.vehicle_types as b', 'a.vehicle_type_id', '=', 'b.id')
            ->select('a.*', 'b.*')
            ->get();
        dd($vehicles);
    }
}
