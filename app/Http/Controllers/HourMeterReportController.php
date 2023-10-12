<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HourMeterReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $start_date = $request->input('start_day');
            $end_date = $request->input('end_day');
            $device_imei = $request->input('device_imei');

            $result = DB::table('hour_meter_report')
                ->where('start_time', '>=', $start_date)
                ->where('start_time', '<=', $end_date)
                ->where('deviceimei', $device_imei)
                ->select(
                    'vehicle_id',
                    'deviceimei',
                    'start_hourmeter',
                    'end_hourmeter',
                    'vehicle_name',
                    'flag',
                    's_lat',
                    's_lng',
                    'total_km',
                    'e_lat',
                    'e_lng',
                    'type_id',
                    'fuel_usage',
                    'fuel_filled',
                    'initial_ltr',
                    'end_ltr',
                    'car_battery',
                    'device_battery',
                    'start_odometer',
                    'end_odometer',
                    'start_hourmeter',
                    'end_hourmeter',
                    'real_start_odo',
                    'real_end_odo',
                    'start_location',
                    'end_location',
                    'start_time',
                    'end_time',
                    DB::raw('TIMEDIFF(end_time, start_time) AS time_difference'), // Corrected closing parenthesis
                    DB::raw('end_hourmeter-start_hourmeter AS total_hour_meter') // Corrected closing parenthesis
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
