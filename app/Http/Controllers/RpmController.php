<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RpmController extends Controller
{
    public function index(Request $request)
    {

        try {
            $start_date = $request->input('start_day');
            $end_date = $request->input('end_day');
            $device_imei = $request->input('device_imei');

            $result = DB::table('rpm_reports')
                ->where('start_time', '>=', $start_date)
                ->where('start_time', '<=', $end_date)
                ->where('deviceimei', $device_imei)
                ->select(
                    'vehicle_id',
                    'deviceimei',
                    'start_hour_meter',
                    'end_hour_meter',
                    'vehicle_name',
                    'rpm_type',
                    'flag',
                    'start_time',
                    'end_time',
                    DB::raw('TIMEDIFF(end_time, start_time) AS time_difference') // Corrected closing parenthesis
                )
                ->get();




            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response($th, 500);
        }
    }
}
