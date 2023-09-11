<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\LiveData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Vehicle;
use Carbon\Carbon;

class LiveDataController extends Controller
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
                F.device_make,
                G.device_model,
                A.device_make_id,
                A.device_model_id,
                B.vehicle_id
            ')
                ->leftJoin('vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
                ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
                ->leftJoin('configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
                ->leftJoin('twings.device_makes as F', 'F.id', '=', 'A.device_make_id')
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
                F.device_make,
                G.device_model,
                A.device_make_id,
                A.device_model_id,
                B.vehicle_id
        ')
                ->leftJoin('vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
                ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
                ->leftJoin('configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
                ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
                ->leftJoin('twings.device_makes as F', 'F.id', '=', 'A.device_make_id')
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
            F.device_make,
            G.device_model,
            A.device_make_id,
            A.device_model_id,
            B.vehicle_id
        ')
            ->leftJoin('vehicles as A', 'B.deviceimei', '=', 'A.device_imei')
            ->leftJoin('twings.vehicle_types as C', 'A.vehicle_type_id', '=', 'C.id')
            ->leftJoin('configurations as D', 'B.vehicle_id', '=', 'D.vehicle_id')
            ->leftJoin(DB::raw("(SELECT device_imei, max(odometer), min(odometer), round(max(odometer) - min(odometer), 2) AS today_distance FROM play_back_histories WHERE DATE_ADD(device_datetime, INTERVAL 330 MINUTE) >= '$startDate' AND DATE_ADD(device_datetime, INTERVAL 330 MINUTE) <= '$endDate' GROUP by device_imei) AS E"), 'E.device_imei', '=', 'A.device_imei')
            ->leftJoin('twings.device_makes as F', 'F.id', '=', 'A.device_make_id')
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
        //inactive
        $live_datas = DB::table('live_data')
            ->where('device_updatedtime', '<', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->get();

        foreach ($live_datas as $live_data) {
            // print_r($live_data);
            DB::table('live_data')
                ->where('id', $live_data->id)
                ->update(['vehicle_current_status' => 5]);
        }

        //expiry date
        $expiry_datas = DB::table('live_data as a')
            ->join('vehicles as b', 'a.deviceimei', '=', 'b.device_imei')
            ->whereBetween('b.expire_date', [DB::raw('CURDATE()'), DB::raw('DATE_ADD(CURDATE(), INTERVAL 15 DAY)')])
            ->select("a.id")
            ->get();

        foreach ($expiry_datas as $expiry_data) {
            // print_r($live_data);
            DB::table('live_data')
                ->where('id', $expiry_data->id)
                ->update(['expiry_status' => 1]);
        }

        $expired_datas = DB::table('live_data as a')
            ->join('vehicles as b', 'a.deviceimei', '=', 'b.device_imei')
            ->where('b.expire_date', '<', DB::raw('CURDATE()'))
            ->select("a.id")
            ->get();

        foreach ($expired_datas as $expired_data) {
            // print_r($live_data);
            DB::table('live_data')
                ->where('id', $expired_data->id)
                ->update(['vehicle_current_status' => 6]);
        }

        $total_vehicles = Vehicle::count();

        $parking = DB::table('live_data')
            ->where('vehicle_current_status', 1)
            ->count();

        $idle = DB::table('live_data')
            ->where('vehicle_current_status', 2)
            ->count();

        $moving = DB::table('live_data')
            ->where('vehicle_current_status', 3)
            ->count();

        $no_data = DB::table('live_data')
            ->where('vehicle_current_status', 4)
            ->count();

        $inactive = DB::table('live_data')
            ->where('vehicle_current_status', 5)
            ->count();

        // dd($parking);
        // dd($idle);
        // dd($moving);
        // dd($inactive);

        $expired_vehicles = Vehicle::where('expire_date', '<', now())->count();

        $expiry_vehicles = Vehicle::whereBetween('expire_date', [DB::raw('CURDATE()'), DB::raw('DATE_ADD(CURDATE(), INTERVAL 15 DAY)')])
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

    public function role_based_vehicle_count(Request $request)
    {
        $user_id = $request->input('user_id');

        $data = User::find($user_id);
        $role_id = $data->role_id;

        if ($role_id == 1) {
            $vehicle_data = DB::table('vehicles')
                ->pluck('id');
        } else if ($role_id == 2) {
            $admin_id = $data->admin_id;
            $vehicle_data = DB::table('vehicles')
                ->where('admin_id', $admin_id)
                ->pluck('id');
        } else if ($role_id == 3) {
            $distributor_id = $data->distributor_id;
            $vehicle_data = DB::table('vehicles')
                ->where('distributor_id', $distributor_id)
                ->pluck('id');
        } else if ($role_id == 4) {
            $dealer_id = $data->dealer_id;
            $vehicle_data = DB::table('vehicles')
                ->where('dealer_id', $dealer_id)
                ->pluck('id');
        } else if ($role_id == 5) {
            $subdealer_id = $data->subdealer_id;
            $vehicle_data = DB::table('vehicles')
                ->where('subdealer_id', $subdealer_id)
                ->pluck('id');
        }

        // dd($vehicle_data);
        $total_vehicles = Vehicle::count();

        $parking = DB::table('live_data')
            ->where('vehicle_current_status', 1)
            ->whereIn('vehicle_id', $vehicle_data)
            ->count();

        $idle = DB::table('live_data')
            ->where('vehicle_current_status', 2)
            ->whereIn('vehicle_id', $vehicle_data)
            ->count();

        $moving = DB::table('live_data')
            ->where('vehicle_current_status', 3)
            ->whereIn('vehicle_id', $vehicle_data)
            ->count();

        $no_data = DB::table('live_data')
            ->where('vehicle_current_status', 4)
            ->whereIn('vehicle_id', $vehicle_data)
            ->count();

        $inactive = DB::table('live_data')
            ->where('vehicle_current_status', 5)
            ->whereIn('vehicle_id', $vehicle_data)
            ->count();

        $expired_vehicles = Vehicle::where('expire_date', '<', now())
            ->whereIn('id', $vehicle_data)
            ->count();

        $expiry_vehicles = Vehicle::whereBetween('expire_date', [DB::raw('CURDATE()'), DB::raw('DATE_ADD(CURDATE(), INTERVAL 15 DAY)')])
            ->whereIn('id', $vehicle_data)
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
