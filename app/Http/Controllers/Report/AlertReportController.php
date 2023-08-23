<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertReportController extends Controller
{
    public function all_alert()
    {
        $alertReports = DB::table('alert_reports')
            ->select('vehicle_name', 'type_id', 'created_at')
            ->get();

        if ($alertReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Alerts Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $alertReports, "status_code" => 200];
        return response($response, 200);
    }
    public function device_alert($id)
    {
        $alertReports = DB::table('alert_reports')
            ->where('vehicle_id', $id)
            ->select('vehicle_name', 'type_id', 'created_at')
            ->get();

        if ($alertReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Alerts Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $alertReports, "status_code" => 200];
        return response($response, 200);
    }
}
