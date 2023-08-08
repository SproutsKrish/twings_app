<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\LiveData;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class LiveDataController extends BaseController
{
    public function multi_dashboard(Request $request)
    {
        $search = $request->input('search');
        if ($search == null) {
            $result = DB::table(function ($query) {
                $query->select('*', DB::raw('ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn'))
                    ->from('live_data');
            }, 'ranked')
                ->select('ranked.*', 'B.vehicle_name', 'B.vehicle_make', 'B.vehicle_model', 'B.vehicle_year', 'C.vehicle_type')
                ->join('vehicles AS B', 'ranked.vehicle_id', '=', 'B.id')
                ->join('vehicle_types AS C', 'B.vehicle_type_id', '=', 'C.id')
                ->where('rn', '=', 1)
                ->orderBy('ranked.id')
                ->get();
            if (!$result) {
                return $this->sendError('No Live Data Found');
            }
            return $this->sendSuccess($result);
        } else {
            $vehicles = Vehicle::where('vehicle_name', 'LIKE', "%$search%")->pluck('id');

            $result = DB::table(function ($query) {
                $query->select('*', DB::raw('ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn'))
                    ->from('live_data');
            }, 'ranked')
                ->select('ranked.*', 'B.vehicle_name', 'B.vehicle_make', 'B.vehicle_model', 'B.vehicle_year', 'C.vehicle_type')
                ->join('vehicles AS B', 'ranked.vehicle_id', '=', 'B.id')
                ->join('vehicle_types AS C', 'B.vehicle_type_id', '=', 'C.id')
                ->where('rn', '=', 1)
                ->whereIn('vehicle_id', $vehicles)
                ->orderBy('ranked.id')
                ->get();
            if (!$result) {
                return $this->sendError('No Live Data Found');
            }
            return $this->sendSuccess($result);
        }
    }

    public function single_dashboard($id)
    {
        $data['vehicle_details'] = DB::table('live_data AS A')
            ->select('A.*', 'B.vehicle_name', 'B.vehicle_make', 'B.vehicle_model', 'B.vehicle_year', 'C.vehicle_type')
            ->join('vehicles AS B', 'A.vehicle_id', '=', 'B.id')
            ->join('vehicle_types AS C', 'B.vehicle_type_id', '=', 'C.id')
            ->where('A.vehicle_id', $id)
            ->orderByDesc('A.id')
            ->limit(1)
            ->get();
        $data['live_details'] = LiveData::select('lattitute', 'longitute', 'speed', 'angle')->where('vehicle_id', $id)->get();

        if (!$data) {
            return $this->sendError('Live Data Not Found');
        }

        return $this->sendSuccess($data);
    }

    public function vehicle_count()
    {
        $total_vehicles = Vehicle::count();

        $no_data = Vehicle::whereNotIn('id', function ($query) {
            $query->select('vehicle_id')
                ->from('live_data');
        })
            ->count();

        $inactive = DB::table('live_data')
            ->whereNotIn('vehicle_id', function ($query) {
                $query->select('vehicle_id')
                    ->from(function ($subquery) {
                        $subquery->select('vehicle_id', DB::raw('ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn'))
                            ->from('live_data')
                            ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'));
                    }, 'ranked');
            })
            ->distinct()
            ->count('vehicle_id');


        $moving = DB::table(function ($subquery) {
            $subquery->select('*', DB::raw('ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn'))
                ->from('live_data')
                ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'));
        }, 'ranked')
            ->join('vehicles as B', 'ranked.vehicle_id', '=', 'B.id')
            ->join('vehicle_types as C', 'B.vehicle_type_id', '=', 'C.id')
            ->where('rn', 1)
            ->where('ignition', 1)
            ->where('speed', '>', 0)
            ->count();

        $parking = DB::table(function ($subquery) {
            $subquery->select('*', DB::raw('ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn'))
                ->from('live_data')
                ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'));
        }, 'ranked')
            ->join('vehicles as B', 'ranked.vehicle_id', '=', 'B.id')
            ->join('vehicle_types as C', 'B.vehicle_type_id', '=', 'C.id')
            ->where('rn', 1)
            ->where('ignition', 0)
            ->where('speed', 0)
            ->count();


        $idle = DB::table(function ($subquery) {
            $subquery->select('*', DB::raw('ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn'))
                ->from('live_data')
                ->where('device_updatedtime', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 10 MINUTE)'));
        }, 'ranked')
            ->join('vehicles as B', 'ranked.vehicle_id', '=', 'B.id')
            ->join('vehicle_types as C', 'B.vehicle_type_id', '=', 'C.id')
            ->where('rn', 1)
            ->where('ignition', 1)
            ->where('speed', 0)
            ->count();

        $expired_vehicles = Vehicle::where('expire_date', '<=', now())->count();

        $expiry_vehicles = Vehicle::where('expire_date', '>', now())
            ->where('expire_date', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL 15 DAY)'))
            ->count();

        $vehicle_count = array('running' => $moving, 'idle' => $idle, 'stop' => $parking, 'no_data' => $no_data,  'inactive' => $inactive, 'total_vehicles' =>  $total_vehicles, 'expired_vehicles' => $expired_vehicles, 'expiry_vehicles' => $expiry_vehicles);

        if (!$vehicle_count) {
            return $this->sendError('Live Data Not Found');
        }

        return $this->sendSuccess($vehicle_count);
    }
}
