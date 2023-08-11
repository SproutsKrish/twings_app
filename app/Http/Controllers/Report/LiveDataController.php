<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\LiveData;
use App\Models\PlayBackHistory;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class LiveDataController extends BaseController
{
    public function multi_dashboard(Request $request)
    {
        $search = $request->input('search');
        if ($search == null) {
            $result = DB::table('vehicles AS A')
                ->select('A.vehicle_name', 'A.expire_date', 'B.vehicle_current_status', 'B.odometer', 'B.speed', 'B.angle', 'B.ignition', 'B.lattitute', 'B.longitute', 'B.device_updatedtime', 'B.gpssignal', 'B.battery_percentage', 'B.door_status', 'B.power_status', 'B.today_distance', 'B.last_ignition_off_time', DB::raw("TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), '%H:%i:%s') as last_dur"), 'A.vehicle_type_id', 'C.vehicle_type')
                ->join('live_data AS B', 'A.id', '=', 'B.vehicle_id')
                ->join('vehicle_types AS C', 'C.id', '=', 'A.vehicle_type_id')
                ->get();

            if (!$result) {
                return $this->sendError('No Live Data Found');
            }
            return $this->sendSuccess($result);
        } else {

            $vehicles = Vehicle::where('vehicle_name', 'LIKE', "%$search%")->pluck('id');

            $result = DB::table('vehicles AS A')
                ->select('A.vehicle_name', 'A.expire_date', 'B.vehicle_current_status', 'B.odometer', 'B.speed', 'B.angle', 'B.ignition', 'B.lattitute', 'B.longitute', 'B.device_updatedtime', 'B.gpssignal', 'B.battery_percentage', 'B.door_status', 'B.power_status', 'B.today_distance', 'B.last_ignition_off_time', DB::raw("TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), '%H:%i:%s') as last_dur"), 'A.vehicle_type_id', 'C.vehicle_type')
                ->join('live_data AS B', 'A.id', '=', 'B.vehicle_id')
                ->join('vehicle_types AS C', 'C.id', '=', 'A.vehicle_type_id')
                ->whereIn('vehicle_id', $vehicles)
                ->get();

            if (!$result) {
                return $this->sendError('No Live Data Found');
            }
            return $this->sendSuccess($result);
        }
    }

    public function single_dashboard($id)
    {
        $data['vehicle'] = DB::table('vehicles AS A')
            ->select('A.vehicle_name', 'A.expire_date', 'B.odometer', 'B.speed', 'B.angle', 'B.ignition', 'B.lattitute', 'B.longitute', 'B.device_updatedtime', 'B.gpssignal', 'B.battery_percentage', 'B.door_status', 'B.power_status', 'B.today_distance', 'B.last_ignition_off_time', DB::raw("TIME_FORMAT(TIMEDIFF(NOW(), B.last_ignition_off_time), '%H:%i:%s') as last_dur"), 'A.vehicle_type_id', 'C.vehicle_type')
            ->join('live_data AS B', 'A.id', '=', 'B.vehicle_id')
            ->join('vehicle_types AS C', 'C.id', '=', 'A.vehicle_type_id')
            ->where('vehicle_id', $id)
            ->get();

        $deviceImei = Vehicle::where('id', $id)->value('device_imei');

        $data['live'] = PlayBackHistory::select('lattitute', 'longitute', 'speed', 'angle')->where('deviceimei', $deviceImei)->get();


        if (!$data) {
            return $this->sendError('Live Data Not Found');
        }

        return $this->sendSuccess($data);
    }

    public function vehicle_count()
    {
        $total_vehicles = Vehicle::count();

        // dd($total_vehicles);

        $no_data = Vehicle::whereNotIn('id', function ($query) {
            $query->select('vehicle_id')
                ->from('live_data');
        })
            ->count();


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
            ->where('vehicle_current_status', 4)
            ->where('device_updatedtime', '<', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'))
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
            return $this->sendError('Live Data Not Found');
        }

        return $this->sendSuccess($vehicle_count);
    }
}
