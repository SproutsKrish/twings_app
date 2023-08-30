<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LiveData;
use App\Models\PlayBackHistory;
use App\Models\PlaybackReport;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class LiveDataController extends BaseController
{
    public function multi_dashboard(Request $request)
    {
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d H:i:s');

        $search = $request->input('search');
        if ($search == null) {
            $result = DB::table('live_data as B')
                ->selectRaw('
                B.id,
                A.vehicle_type_id,
                C.vehicle_type,
                D.speed_limit,
                A.vehicle_name,
                A.device_imei,
                A.expire_date,
                A.safe_parking,
                A.immobilizer_option,
                B.vehicle_current_status,
                B.vehicle_status,
                B.lattitute,
                B.longitute,
                B.ignition,
                B.ac_status,
                B.speed,
                B.angle,
                B.odometer,
                DATE_ADD(B.device_updatedtime, INTERVAL 330 MINUTE) as device_updatedtime,
                B.temperature,
                B.device_battery_volt,
                B.vehicle_battery_volt,
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
                IFNULL(E.today_distance, 0) as today_distance,
                F.device_type,
                G.device_model

            ')
                ->leftJoin('vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
                ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
                ->leftJoin('configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
                ->leftJoin('twings.device_types as F', 'F.id', '=', 'A.device_make_id')
                ->leftJoin('twings.device_models as G', 'G.id', '=', 'A.device_model_id')
                ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
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
                D.speed_limit,
                A.vehicle_name,
                A.device_imei,
                A.expire_date,
                A.safe_parking,
                A.immobilizer_option,
                B.vehicle_current_status,
                B.vehicle_status,
                B.lattitute,
                B.longitute,
                B.ignition,
                B.ac_status,
                B.speed,
                B.angle,
                B.odometer,
                DATE_ADD(B.device_updatedtime, INTERVAL 330 MINUTE) as device_updatedtime,
                B.temperature,
                B.device_battery_volt,
                B.vehicle_battery_volt,
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
                IFNULL(E.today_distance, 0) as today_distance,
                F.device_type,
                G.device_model
        ')
                ->leftJoin('vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
                ->leftJoin('vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
                ->leftJoin('configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
                ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
                ->leftJoin('twings.device_types as F', 'F.id', '=', 'A.device_make_id')
                ->leftJoin('twings.device_models as G', 'G.id', '=', 'A.device_model_id')
                ->whereIn('B.deviceimei', $device_imei)
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
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d H:i:s');

        $result = DB::table('live_data as B')
            ->selectRaw('
            B.id,
            A.vehicle_type_id,
            C.vehicle_type,
            D.speed_limit,
            A.vehicle_name,
            A.device_imei,
            A.expire_date,
            A.safe_parking,
            A.immobilizer_option,
            B.vehicle_current_status,
            B.vehicle_status,
            B.lattitute,
            B.longitute,
            B.ignition,
            B.ac_status,
            B.speed,
            B.angle,
            B.odometer,
            DATE_ADD(B.device_updatedtime, INTERVAL 330 MINUTE) as device_updatedtime,
            B.temperature,
            B.device_battery_volt,
            B.vehicle_battery_volt,
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
            IFNULL(E.today_distance, 0) as today_distance,
            F.device_type,
            G.device_model

        ')
            ->leftJoin('vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
            ->leftJoin('vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
            ->leftJoin('configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
            ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
            ->leftJoin('twings.device_types as F', 'F.id', '=', 'A.device_make_id')
            ->leftJoin('twings.device_models as G', 'G.id', '=', 'A.device_model_id')
            ->where('B.deviceimei', $device_imei)
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
        // echo $current_time = Carbon::now();

        $live_datas = DB::table('live_data')
            ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->get();

        foreach ($live_datas as $live_data) {
            print_r($live_data);
        }


        // $total_vehicles = Vehicle::count();

        // $parking = DB::table('live_data')
        //     ->where('ignition', 0)
        //     ->where('speed', 0)
        //     ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
        //     ->count();

        // $idle = DB::table('live_data')
        //     ->where('ignition', 1)
        //     ->where('speed', 0)
        //     ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
        //     ->count();

        // $moving = DB::table('live_data')
        //     ->where('ignition', 1)
        //     ->where('speed', '>', 0)
        //     ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
        //     ->count();

        // $inactive = DB::table('live_data')
        //     ->where('device_updatedtime', '<', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
        //     ->count();

        // $no_data = DB::table('live_data')
        //     ->where('vehicle_current_status', 4)
        //     ->where('device_updatedtime', null)
        //     ->count();

        // // dd($parking);
        // // dd($idle);
        // // dd($moving);
        // // dd($inactive);

        // $expired_vehicles = Vehicle::where('expire_date', '<=', now())->count();

        // $expiry_vehicles = Vehicle::where('expire_date', '>', now())
        //     ->where('expire_date', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL 15 DAY)'))
        //     ->count();

        // $vehicle_count = array(
        //     'running' => $moving,
        //     'idle' => $idle,
        //     'stop' => $parking,
        //     'no_data' => $no_data,
        //     'inactive' => $inactive,
        //     'total_vehicles' =>  $total_vehicles,
        //     'expired_vehicles' => $expired_vehicles,
        //     'expiry_vehicles' => $expiry_vehicles
        // );

        // if (!$vehicle_count) {
        //     $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
        //     return response()->json($response, 404);
        // }

        // $response = ["success" => true, "data" => $vehicle_count, "status_code" => 200];
        // return response()->json($response, 200);
    }
}
