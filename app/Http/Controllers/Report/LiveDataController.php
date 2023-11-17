<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CustomerConfiguration;
use App\Models\LiveData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use App\Models\Vehicle;
use Carbon\Carbon;

class LiveDataController extends Controller
{

    public function multi_dashboard(Request $request)
    {
        $endDate = date('Y-m-d H:i:s', strtotime('330 minutes'));
        $carbonDatetime = Carbon::parse($endDate);
        $startDate = $carbonDatetime->toDateString() . " 00:00:00";

        // $startDate = date('Y-m-d') . ' 00:00:00';
        // $endDate = date('Y-m-d H:i:s');


        $search = $request->input('search');
        if ($search == null) {
            $result = DB::table('live_data as B')
                ->selectRaw('
                B.id,
                A.vehicle_type_id,
                C.vehicle_type,
                C.short_name,
                D.speed_limit,
                A.vehicle_name,
                A.device_imei,
                A.installation_date,
                A.vehicle_expire_date as expire_date,
                B.safe_parking,
                B.safe_parking_alert,
                A.immobilizer_option,
                CASE
                    WHEN B.ignition = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 1
                    WHEN B.ignition = 1 AND B.speed = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 2
                    WHEN B.ignition = 1 AND B.speed > 1 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 3
                    WHEN B.device_updatedtime IS NULL THEN 4
                    WHEN B.device_updatedtime < DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 5
                ELSE NULL
                END AS vehicle_current_status,
                CASE
                    WHEN B.ignition = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Parking"
                    WHEN B.ignition = 1 AND B.speed = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Idle"
                    WHEN B.ignition = 1 AND B.speed > 1 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Moving"
                    WHEN B.device_updatedtime IS NULL THEN "No Data"
                    WHEN B.device_updatedtime < DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "InActive"
                ELSE NULL
                END AS current_status,
                B.vehicle_status,
                B.lattitute,
                B.longitute,
                B.ignition,
                B.ac_status,
                B.altitude,
                ROUND(B.speed, 0) AS speed,
                B.angle,
                B.odometer,
                DATE_ADD(B.device_updatedtime, INTERVAL 330 MINUTE) as device_updatedtime,
                B.temperature,
                ROUND(B.device_battery_volt, 0) AS device_battery_volt,
                ROUND(B.vehicle_battery_volt, 0) AS vehicle_battery_volt,
                B.battery_percentage,
                B.door_status,
                B.power_status,
                DATE_ADD(B.last_ignition_on_time, INTERVAL 330 MINUTE) as last_ignition_on_time,
                DATE_ADD(B.last_ignition_off_time, INTERVAL 330 MINUTE) as last_ignition_off_time,
                TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), "%H:%i:%s") as last_duration,
                B.fuel_litre,
                B.immobilizer_status,
                B.gpssignal,
                B.gsm_status,
                B.rpm_value,
                B.sec_engine_status,
                B.expiry_status,
                IFNULL(FLOOR(E.today_distance), 0) AS today_distances,
                IFNULL(
                    SUBSTRING(
                        CAST(E.today_distance AS CHAR),
                        LOCATE(' . ', CAST(E.today_distance AS CHAR)) + 1,
                        2
                    ),
                    0
                ) AS today_distance_decimal_part,
                IFNULL(E.today_distance, 0) as today_distance,
                F.device_make,
                G.device_model,
                A.device_make_id,
                A.device_model_id,
                B.vehicle_id,
                A.sim_mob_no,
                K.package_name

            ')
                ->leftJoin('twings.vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
                ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
                ->leftJoin('twings.configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
                ->leftJoin('twings.licenses as I', 'I.vehicle_id', '=', 'A.id')
                ->leftJoin('twings.plans as J', 'J.id', '=', 'I.plan_id')
                ->leftJoin('twings.packages as K', 'K.id', '=', 'J.package_id')
                ->leftJoin('twings.device_makes as F', 'F.id', '=', 'A.device_make_id')
                ->leftJoin('twings.device_models as G', 'G.id', '=', 'A.device_model_id')
                ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
                ->where('A.status', 1)
                ->get();

            if ($result->isEmpty()) {
                $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
                return response($response, 404);
            }
            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response($response, 200);
        } else {
            $device_imei = Vehicle::where('vehicle_name', 'LIKE', "%$search%")->pluck('device_imei');

            $result = DB::table('live_data as B')
                ->selectRaw('
                B.id,
                A.vehicle_type_id,
                C.vehicle_type,
                C.short_name,
                D.speed_limit,
                A.vehicle_name,
                A.device_imei,
                A.installation_date,
                A.vehicle_expire_date as expire_date,
                B.safe_parking,
                B.safe_parking_alert,
                A.immobilizer_option,
                CASE
                    WHEN B.ignition = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 1
                    WHEN B.ignition = 1 AND B.speed = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 2
                    WHEN B.ignition = 1 AND B.speed > 1 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 3
                    WHEN B.device_updatedtime IS NULL THEN 4
                    WHEN B.device_updatedtime < DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 5
                ELSE NULL
                END AS vehicle_current_status,
                CASE
                    WHEN B.ignition = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Parking"
                    WHEN B.ignition = 1 AND B.speed = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Idle"
                    WHEN B.ignition = 1 AND B.speed > 1 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Moving"
                    WHEN B.device_updatedtime IS NULL THEN "No Data"
                    WHEN B.device_updatedtime < DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "InActive"
                ELSE NULL
                END AS current_status,
                B.vehicle_status,
                B.lattitute,
                B.longitute,
                B.ignition,
                B.ac_status,
                B.altitude,
                ROUND(B.speed, 0) AS speed,
                B.angle,
                B.odometer,
                DATE_ADD(B.device_updatedtime, INTERVAL 330 MINUTE) as device_updatedtime,
                B.temperature,
                ROUND(B.device_battery_volt, 0) AS device_battery_volt,
                ROUND(B.vehicle_battery_volt, 0) AS vehicle_battery_volt,
                B.battery_percentage,
                B.door_status,
                B.power_status,
                DATE_ADD(B.last_ignition_on_time, INTERVAL 330 MINUTE) as last_ignition_on_time,
                DATE_ADD(B.last_ignition_off_time, INTERVAL 330 MINUTE) as last_ignition_off_time,
                TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), "%H:%i:%s") as last_duration,
                B.fuel_litre,
                B.immobilizer_status,
                B.gpssignal,
                B.gsm_status,
                B.rpm_value,
                B.sec_engine_status,
                B.expiry_status,
                IFNULL(FLOOR(E.today_distance), 0) AS today_distances,
                IFNULL(
                    SUBSTRING(
                        CAST(E.today_distance AS CHAR),
                        LOCATE(' . ', CAST(E.today_distance AS CHAR)) + 1,
                        2
                    ),
                    0
                ) AS today_distance_decimal_part,
                IFNULL(E.today_distance, 0) as today_distance,
                F.device_make,
                G.device_model,
                A.device_make_id,
                A.device_model_id,
                B.vehicle_id,
                A.sim_mob_no,
                K.package_name
        ')
                ->leftJoin('twings.vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
                ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
                ->leftJoin('twings.configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
                ->leftJoin('twings.licenses as I', 'I.vehicle_id', '=', 'A.id')
                ->leftJoin('twings.plans as J', 'J.id', '=', 'I.plan_id')
                ->leftJoin('twings.packages as K', 'K.id', '=', 'J.package_id')
                ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
                ->leftJoin('twings.device_makes as F', 'F.id', '=', 'A.device_make_id')
                ->leftJoin('twings.device_models as G', 'G.id', '=', 'A.device_model_id')
                ->whereIn('B.deviceimei', $device_imei)
                ->where('A.status', 1)
                ->get();

            if ($result->isEmpty()) {
                $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
                return response($response, 404);
            }
            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response($response, 200);
        }
    }

    public function single_dashboard($device_imei)
    {
        // $startDate = date('Y-m-d H:i:s', strtotime('-330 minutes'));
        // $endDate = date('Y-m-d H:i:s', strtotime('-330 minutes'));

        $endDate = date('Y-m-d H:i:s', strtotime('330 minutes'));
        $carbonDatetime = Carbon::parse($endDate);
        $startDate = $carbonDatetime->toDateString() . " 00:00:00";


        $result = DB::table('live_data as B')
            ->selectRaw('
            B.id,
            A.vehicle_type_id,
            C.vehicle_type,
            C.short_name,
            D.speed_limit,
            A.vehicle_name,
            A.device_imei,
            A.installation_date,
            A.vehicle_expire_date as expire_date,
            B.safe_parking,
            B.safe_parking_alert,
            A.immobilizer_option,
            CASE
                WHEN B.ignition = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 1
                WHEN B.ignition = 1 AND B.speed = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 2
                WHEN B.ignition = 1 AND B.speed > 1 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 3
                WHEN B.device_updatedtime IS NULL THEN 4
                WHEN B.device_updatedtime < DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 5
            ELSE NULL
            END AS vehicle_current_status,
            CASE
                WHEN B.ignition = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Parking"
                WHEN B.ignition = 1 AND B.speed = 0 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Idle"
                WHEN B.ignition = 1 AND B.speed > 1 AND B.device_updatedtime >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "Moving"
                WHEN B.device_updatedtime IS NULL THEN "No Data"
                WHEN B.device_updatedtime < DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN "InActive"
            ELSE NULL
            END AS current_status,
            B.vehicle_status,
            B.lattitute,
            B.longitute,
            B.ignition,
            B.ac_status,
            B.altitude,
            ROUND(B.speed, 0) AS speed,
            B.angle,
            B.odometer,
            DATE_ADD(B.device_updatedtime, INTERVAL 330 MINUTE) as device_updatedtime,
            B.temperature,
            ROUND(B.device_battery_volt, 0) AS device_battery_volt,
            ROUND(B.vehicle_battery_volt, 0) AS vehicle_battery_volt,
            B.battery_percentage,
            B.door_status,
            B.power_status,
            DATE_ADD(B.last_ignition_on_time, INTERVAL 330 MINUTE) as last_ignition_on_time,
            DATE_ADD(B.last_ignition_off_time, INTERVAL 330 MINUTE) as last_ignition_off_time,
            TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), "%H:%i:%s") as last_duration,
            B.fuel_litre,
            B.immobilizer_status,
            B.gpssignal,
            B.gsm_status,
            B.rpm_value,
            B.sec_engine_status,
            B.expiry_status,
            IFNULL(FLOOR(E.today_distance), 0) AS today_distances,
            IFNULL(
                SUBSTRING(
                    CAST(E.today_distance AS CHAR),
                    LOCATE(' . ', CAST(E.today_distance AS CHAR)) + 1,
                    2
                ),
                0
            ) AS today_distance_decimal_part,
            IFNULL(E.today_distance, 0) as today_distance,
            F.device_make,
            G.device_model,
            A.device_make_id,
            A.device_model_id,
            B.vehicle_id,
            A.sim_mob_no,
            K.package_name
        ')
            ->leftJoin('twings.vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
            ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
            ->leftJoin('twings.configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
            ->leftJoin('twings.licenses as I', 'I.vehicle_id', '=', 'A.id')
            ->leftJoin('twings.plans as J', 'J.id', '=', 'I.plan_id')
            ->leftJoin('twings.packages as K', 'K.id', '=', 'J.package_id')
            ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
            ->leftJoin('twings.device_makes as F', 'F.id', '=', 'A.device_make_id')
            ->leftJoin('twings.device_models as G', 'G.id', '=', 'A.device_model_id')
            ->where('B.deviceimei', $device_imei)
            ->where('A.status', 1)
            ->first();

        if (empty($result)) {
            $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $result, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function vehicle_count()
    {
        $expiry_datas = Vehicle::whereBetween('vehicle_expire_date', [DB::raw('CURDATE()'), DB::raw('DATE_ADD(CURDATE(), INTERVAL 15 DAY)')])
            ->select("id")
            ->get();

        foreach ($expiry_datas as $expiry_data) {
            LiveData::where('vehicle_id', $expiry_data->id)
                ->update(['expiry_status' => 1]);
        }

        $expired_datas = Vehicle::where('vehicle_expire_date', '<', DB::raw('CURDATE()'))
            ->select("id")
            ->get();


        foreach ($expired_datas as $expired_data) {
            LiveData::where('vehicle_id', $expired_data->id)
                ->update(['expiry_status' => 2]);
        }

        $total_vehicles = LiveData::where('vehicle_status', 1)->count();

        $parking = LiveData::where('ignition', 0)
            ->where('device_updatedtime', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->where('vehicle_status', 1)
            ->count();

        $idle = LiveData::where('ignition', 1)
            ->where('speed', 0)
            ->where('device_updatedtime', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->where('vehicle_status', 1)
            ->count();

        $moving = LiveData::where('ignition', 1)
            ->where('speed', '>', 0)
            ->where('device_updatedtime', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->where('vehicle_status', 1)
            ->count();

        $no_data = LiveData::where('device_updatedtime', '=', null)
            ->where('vehicle_status', 1)
            ->count();

        $inactive = LiveData::where('device_updatedtime', '<', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->where('vehicle_status', 1)
            ->count();

        $expiry_vehicles = Vehicle::whereBetween('vehicle_expire_date', [DB::raw('CURDATE()'), DB::raw('DATE_ADD(CURDATE(), INTERVAL 15 DAY)')])
            ->where('status', 1)
            ->count();

        $expired_vehicles = Vehicle::where('vehicle_expire_date', '<', now())
            ->where('status', 2)
            ->count();

        $vehicle_count = array(
            'running' => $moving,
            'idle' => $idle,
            'stop' => $parking,
            'no_data' => $no_data,
            'inactive' => $inactive,
            'total_vehicles' =>  $total_vehicles,
            'expired_vehicles' => $expired_vehicles,
            'expiry_vehicles' => $expiry_vehicles
        );

        if (!$vehicle_count) {
            $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $vehicle_count, "status_code" => 200];
        return response()->json($response, 200);
    }
}
