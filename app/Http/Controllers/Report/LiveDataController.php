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
        $search = $request->input('search');
        if ($search == null) {
            $result = DB::table('vehicles AS A')
                ->select('B.id', 'A.vehicle_type_id', 'A.vehicle_name', 'A.device_imei', 'A.expire_date', 'A.safe_parking', 'A.immobilizer_option', 'C.vehicle_type', 'D.speed_limit', 'B.vehicle_current_status', 'B.vehicle_status', 'B.lattitute', 'B.longitute', 'B.ignition', 'B.ac_status', 'B.speed', 'B.angle', 'B.odometer', DB::raw("DATE_ADD(B.device_updatedtime, INTERVAL '330' MINUTE) as device_updatedtime"), 'B.temperature', 'B.device_battery_volt', 'B.vehicle_battery_volt', 'B.battery_percentage', 'B.door_status', 'B.power_status', 'B.today_distance', DB::raw("DATE_ADD(B.last_ignition_on_time, INTERVAL '330' MINUTE) as last_ignition_on_time"), DB::raw("DATE_ADD(B.last_ignition_off_time, INTERVAL '330' MINUTE) as last_ignition_off_time"),  DB::raw("TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), '%H:%i:%s') as last_duration"),  'B.fuel_litre', 'B.immobilizer_status', 'B.gpssignal', 'B.gsm_status', 'B.rpm_value', 'B.sec_engine_status', 'B.expiry_status')
                ->leftJoin('live_data AS B', 'A.id', '=', 'B.vehicle_id')
                ->leftJoin('vehicle_types AS C', 'C.id', '=', 'A.vehicle_type_id')
                ->leftJoin('configurations AS D', 'D.vehicle_id', '=', 'A.id')
                ->get();

            if ($result->isEmpty()) {
                $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
                return response($response, 404);
            }
            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response($response, 200);
        } else {

            $deviceimei = Vehicle::where('vehicle_name', 'LIKE', "%$search%")->pluck('device_imei');

            $result = DB::table('vehicles AS A')
                ->select('B.id', 'A.vehicle_type_id', 'A.vehicle_name', 'A.device_imei', 'A.expire_date', 'A.safe_parking', 'A.immobilizer_option', 'C.vehicle_type', 'D.speed_limit', 'B.vehicle_current_status', 'B.vehicle_status', 'B.lattitute', 'B.longitute', 'B.ignition', 'B.ac_status', 'B.speed', 'B.angle', 'B.odometer', DB::raw("DATE_ADD(B.device_updatedtime, INTERVAL '330' MINUTE) as device_updatedtime"), 'B.temperature', 'B.device_battery_volt', 'B.vehicle_battery_volt', 'B.battery_percentage', 'B.door_status', 'B.power_status', 'B.today_distance', DB::raw("DATE_ADD(B.last_ignition_on_time, INTERVAL '330' MINUTE) as last_ignition_on_time"), DB::raw("DATE_ADD(B.last_ignition_off_time, INTERVAL '330' MINUTE) as last_ignition_off_time"),  DB::raw("TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), '%H:%i:%s') as last_duration"),  'B.fuel_litre', 'B.immobilizer_status', 'B.gpssignal', 'B.gsm_status', 'B.rpm_value', 'B.sec_engine_status', 'B.expiry_status')
                ->leftJoin('live_data AS B', 'A.id', '=', 'B.vehicle_id')
                ->leftJoin('vehicle_types AS C', 'C.id', '=', 'A.vehicle_type_id')
                ->leftJoin('configurations AS D', 'D.vehicle_id', '=', 'A.id')

                ->whereIn('B.deviceimei', $deviceimei)
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
        $vehicle_data = DB::table('vehicles AS A')
            ->select('B.id', 'A.vehicle_type_id', 'A.vehicle_name', 'A.device_imei', 'A.expire_date', 'A.safe_parking', 'A.immobilizer_option', 'C.vehicle_type', 'B.vehicle_current_status',  'B.vehicle_status', 'B.lattitute', 'B.longitute', 'B.ignition', 'B.ac_status', 'B.speed', 'B.angle', 'B.odometer', DB::raw("DATE_ADD(B.device_updatedtime, INTERVAL '330' MINUTE) as device_updatedtime"), 'B.temperature', 'B.device_battery_volt', 'B.vehicle_battery_volt', 'B.battery_percentage', 'B.door_status', 'B.power_status', 'B.today_distance', DB::raw("DATE_ADD(B.last_ignition_on_time, INTERVAL '330' MINUTE) as last_ignition_on_time"), DB::raw("DATE_ADD(B.last_ignition_off_time, INTERVAL '330' MINUTE) as last_ignition_off_time"),  DB::raw("TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), '%H:%i:%s') as last_duration"),  'B.fuel_litre', 'B.immobilizer_status', 'B.gpssignal', 'B.gsm_status', 'B.rpm_value', 'B.sec_engine_status', 'B.expiry_status')
            ->join('live_data AS B', 'A.id', '=', 'B.vehicle_id')
            ->join('vehicle_types AS C', 'C.id', '=', 'A.vehicle_type_id')
            ->where('B.deviceimei', $device_imei)
            ->first();

        // $deviceImei = Vehicle::where('id', $id)->value('device_imei');

        // $data['live'] = PlaybackReport::select('latitude', 'longitude', 'speed', 'angle')->where('device_imei', $deviceImei)->orderBy('id', 'desc')->get();

        // $data['live'] = PlaybackReport::select('latitude', 'longitude', 'speed', 'angle')
        //     ->where('device_imei', $deviceImei)
        //     ->where('device_datetime', '>=', Carbon::now()->subMinutes(2))
        //     ->get();

        if (empty($vehicle_data)) {
            $response = ["success" => false, "message" => 'No Live Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $vehicle_data, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function vehicle_count()
    {
        $total_vehicles = Vehicle::count();

        // dd($total_vehicles);

        // $no_data = Vehicle::whereNotIn('id', function ($query) {
        //     $query->select('vehicle_id')
        //         ->from('live_data');
        // })
        //     ->count();


        // dd($no_data);

        $parking = DB::table('live_data')
            ->where('ignition', 0)
            ->where('speed', 0)
            ->where('vehicle_current_status', 1)
            ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->count();

        $idle = DB::table('live_data')
            ->where('ignition', 1)
            ->where('speed', 0)
            ->where('vehicle_current_status', 2)
            ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->count();

        $moving = DB::table('live_data')
            ->where('ignition', 1)
            ->where('speed', '>', 0)
            ->where('vehicle_current_status', 3)
            ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->count();

        $inactive = DB::table('live_data')
            ->where('device_updatedtime', '<', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
            ->count();

        $no_data = DB::table('live_data')
            ->where('vehicle_current_status', 4)
            ->where('device_updatedtime', null)
            ->count();

        // dd($parking);
        // dd($idle);
        // dd($moving);
        // dd($inactive);

        $expired_vehicles = Vehicle::where('expire_date', '<=', now())->count();

        $expiry_vehicles = Vehicle::where('expire_date', '>', now())
            ->where('expire_date', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL 15 DAY)'))
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
