<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExecutiveReport;


class SmartReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $start_date = $request->input('start_day');
            $end_date = $request->input('end_day');
            $device_imei = $request->input('device_imei');

            $result = ExecutiveReport::getSmartReports($start_date, $end_date, $device_imei);

            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response($th, 500);
        }
    }
}
