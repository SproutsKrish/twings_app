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

        $results = DB::select("
        SELECT
            date,
            min_datetime as start_date,
            (SELECT latitude FROM play_back_histories WHERE device_datetime = min_datetime) AS start_latitude,
            (SELECT longitude FROM play_back_histories WHERE device_datetime = min_datetime) AS start_longitude,
            min_odometer as start_odometer,
            max_datetime as end_date,
            (SELECT latitude FROM play_back_histories WHERE device_datetime = max_datetime) AS end_latitude,
            (SELECT longitude FROM play_back_histories WHERE device_datetime = max_datetime) AS end_longitude,
            max_odometer as end_odometer,
            max_odometer - min_odometer AS odometer_difference
        FROM (
            SELECT
                DATE(device_datetime) AS date,
                MAX(device_datetime) AS max_datetime,
                MAX(odometer) AS max_odometer,
                MIN(device_datetime) AS min_datetime,
                MIN(odometer) AS min_odometer
            FROM play_back_histories
            WHERE device_datetime BETWEEN '2023-08-16 06:00:00' AND '2023-08-17 23:59:59' and device_imei = '2109120102295'
            GROUP BY DATE(device_datetime)
        ) AS temp;
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
