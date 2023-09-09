<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\Controller;
use App\Models\AlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlertNotificationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $alert_type_id = $request->input('alert_type_id');
            $user_status = $request->input('user_status');

            $data = DB::table('alert_notifications')
                ->where('alert_type_id', $alert_type_id)
                ->update(['user_status' => $user_status]);
            if ($data) {
                $response = ["success" => true, "message" => 'Alert Notification Saved Successfully', "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => 'Alert Notification Failed to Save', "status_code" => 404];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => $e, "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function alert_notifications_list()
    {
        $alert_notifications_list = DB::table('alert_notifications as a')
            ->join('twings.alert_types as b', 'a.alert_type_id', '=', 'b.id')
            ->where('active_status', '1')
            ->select('a.id', 'a.alert_type_id', 'b.alert_type', 'a.user_status')
            ->get();

        $response = ["success" => true, "data" => $alert_notifications_list, "status_code" => 200];
        return response()->json($response, 200);
    }
}
