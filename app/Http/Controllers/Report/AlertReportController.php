<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertReportController extends Controller
{
    public function all_alert(Request $request)
    {
        $user = User::find($request->input('user_id'))->first();

        $results = DB::table('twings.live_notifications as a')
            ->join('twings.alert_types as b', 'a.alert_type_id', '=', 'b.id')
            ->join('twings.vehicles as c', 'a.device_imei', '=', 'c.device_imei')
            ->where('a.user_id', $user->client_id)
            ->select('a.alert_type_id', 'b.alert_type', 'a.device_imei', 'c.vehicle_name', 'a.lattitute', 'a.longitute', 'a.speed', 'a.odometer', DB::raw("DATE_ADD(a.device_updatedtime, INTERVAL '330' MINUTE) as device_updatedtime"))
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
        $user = User::find($request->input('user_id'))->first();

        $device_imei = $request->input('device_imei');

        $results = DB::table('twings.live_notifications as a')
            ->join('twings.alert_types as b', 'a.alert_type_id', '=', 'b.id')
            ->join('twings.vehicles as c', 'a.device_imei', '=', 'c.device_imei')
            ->where('a.device_imei', $device_imei)
            ->where('a.user_id', $user->client_id)
            ->select('a.alert_type_id', 'b.alert_type', 'a.device_imei', 'c.vehicle_name', 'a.lattitute', 'a.longitute', 'a.speed', 'a.odometer', DB::raw("DATE_ADD(a.device_updatedtime, INTERVAL '330' MINUTE) as device_updatedtime"))
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
