<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\Controller;
use App\Models\AlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlertNotificationController extends Controller
{
    //app
    public function store(Request $request)
    {
        try {
            $alert_type_id = $request->input('alert_type_id');
            $user_status = $request->input('user_status');
            $user_id = $request->input('user_id');

            $data = DB::table('twings.alert_notifications')
                ->where('alert_type_id', $alert_type_id)
                ->where('user_id', $user_id)
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



    //web
    public function update(Request $request)
    {

        try {
            $alert_type_id = $request->input('alert_type_id');
            $user_status = $request->input('user_status');
            $user_id = $request->input('user_id');

            DB::table('twings.alert_notifications')
                ->whereIn('alert_type_id', $alert_type_id)
                ->where('user_id', $user_id)
                ->update(['user_status' => 1]);

            DB::table('twings.alert_notifications')
                ->whereNotIn('alert_type_id', $alert_type_id)
                ->where('user_id', $user_id)
                ->update(['user_status' => 0]);

            $response = ["success" => true, "message" => 'Alert Notification Saved Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = ["success" => false, "message" => $e, "status_code" => 404];
            return response()->json($response, 404);
        }
    }


    public function alert_notifications_list(Request $request)
    {
        $user_id = $request->input('user_id');

        $alert_notifications_list = DB::table('twings.alert_notifications as a')
            ->join('twings.alert_types as b', 'a.alert_type_id', '=', 'b.id')
            ->where('active_status', '1')
            ->where('a.user_id', $user_id)
            ->select('a.id', 'a.alert_type_id', 'b.alert_type', 'a.user_status')
            ->get();

        if ($alert_notifications_list->isEmpty()) {
            $response = ["success" => false, "message" => 'No Alert Type Linked', "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $alert_notifications_list, "status_code" => 200];
            return response()->json($response, 200);
        }
    }
}
