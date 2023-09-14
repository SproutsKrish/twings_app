<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertReportController extends Controller
{
    public function all_alert(Request $request)
    {
        $user_id =  $request->input('user_id');

        $results = DB::table('twings.live_notifications as a')
            ->join('twings.alert_types as b', 'a.alert_type_id', '=', 'b.id')
            ->join('twings.vehicles as c', 'a.deviceimei', '=', 'c.device_imei')
            ->where('a.user_id', $user_id)
            ->select('a.alert_type_id', 'b.alert_type', 'a.deviceimei', 'c.vehicle_name', 'a.lattitute', 'a.longitute', 'a.speed', 'a.odometer', 'a.device_datetime')
            ->orderBy('a.id', 'desc')
            ->get();

        if ($results->isEmpty()) {
            $response = ["success" => false, "message" => 'No Alerts Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response($response, 200);
    }
    public function device_alert(Request $request)
    {
        $user_id =  $request->input('user_id');
        $device_imei = $request->input('device_imei');

        $results = DB::table('twings.live_notifications as a')
            ->join('twings.alert_types as b', 'a.alert_type_id', '=', 'b.id')
            ->join('twings.vehicles as c', 'a.deviceimei', '=', 'c.device_imei')
            ->where('a.device_imei', $device_imei)
            ->where('a.user_id', $user_id)
            ->select('a.alert_type_id', 'b.alert_type', 'a.deviceimei', 'c.vehicle_name', 'a.lattitute', 'a.longitute', 'a.speed', 'a.odometer', 'a.device_datetime')
            ->orderBy('a.id', 'desc')
            ->get();

        if ($results->isEmpty()) {
            $response = ["success" => false, "message" => 'No Alerts Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response($response, 200);
    }
}
