<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcReportController extends Controller
{
    public function ac_report(Request $request)
    {
        // $acReports = DB::table('ac_reports as A')
        //     ->join('vehicles as B', 'A.vehicle_id', '=', 'B.id')
        //     ->where('A.start_datetime', '>=', $request->input('start_day'))
        //     ->where('A.end_datetime', '<=', $request->input('end_day'))
        //     ->where('A.device_imei', '=', $request->input('device_imei'))
        //     ->select('A.*', 'B.vehicle_make', DB::raw("TIME_FORMAT(TIMEDIFF(A.end_datetime, A.start_datetime), '%H:%i:%s') as idle_duration"))
        //     ->get();


        // $results = DB::select("SELECT start_datetime, start_latitude, start_longitude, end_datetime, end_latitude, end_longitude, TIMEDIFF(end_datetime, start_datetime) as duration, end_odometer - start_odometer AS total_kms FROM `ac_reports` WHERE start_datetime >= '2023-08-17 19:14:57' and end_datetime <= '2023-08-18 22:19:57' and device_imei = '2109120102295'");

        $results = DB::table('ac_reports AS A')
            ->select(
                'B.vehicle_name',
                'A.start_datetime',
                'A.start_latitude',
                'A.start_longitude',
                'A.end_datetime',
                'A.end_latitude',
                'A.end_longitude',
                DB::raw('TIMEDIFF(A.end_datetime, A.start_datetime) as duration'),
                DB::raw('A.end_odometer - A.start_odometer AS total_kms')
            )
            ->join('vehicles AS B', 'A.device_imei', '=', 'B.device_imei')
            ->where('A.start_datetime', '>=', $request->input('start_day'))
            ->where('A.end_datetime', '<=', $request->input('end_day'))
            ->where('A.device_imei', $request->input('device_imei'))
            ->get();


        if (empty($results)) {
            $response = ["success" => false, "message" => 'No AC Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response($response, 200);
    }
}
