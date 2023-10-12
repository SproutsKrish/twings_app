<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelController extends Controller
{
    public function index(Request $request)
    {

        try {
            $start_date = $request->input('start_day');
            $end_date = $request->input('end_day');
            $device_imei = $request->input('device_imei');

            $result = DB::table('fuel_fill_dip_reports')
                ->where('start_time', '>=', $start_date)
                ->where('end_time', '<=', $end_date)
                ->where('deviceimei', $device_imei)
                ->select('vehicle_id', 'deviceimei', 'start_fuel', 'end_fuel', 'fuel_difference', 'lattitute', 'longitute', 'location_name', 'start_time', 'end_time')->get();

            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response($th, 500);
        }
    }
}
