<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DistanceReport;
use Illuminate\Support\Facades\DB;

class DistanceReportController extends Controller
{
    public function get_distance_report(Request $request)
    {
        $startDay = $request->input('start_day');
        $endDay = $request->input('end_day');
        $deviceImei = $request->input('device_imei');
        // DB::enableQueryLog();
        $results = DB::table('play_back_histories as pbh')
            ->join('vehicles as v', 'pbh.device_imei', '=', 'v.device_imei')
            ->select(
                'pbh.device_imei',
                'v.vehicle_name',
                DB::raw('DATE(DATE_ADD(pbh.device_datetime, INTERVAL 330 MINUTE)) AS date'),
                DB::raw('MIN(pbh.latitude) AS start_latitude'),
                DB::raw('MIN(pbh.longitude) AS start_longitude'),
                DB::raw('MIN(TIME(DATE_ADD(pbh.device_datetime, INTERVAL 330 MINUTE))) AS start_time'),
                DB::raw('MIN(pbh.odometer) AS start_odometer'),
                DB::raw('MAX(pbh.latitude) AS end_latitude'),
                DB::raw('MAX(pbh.longitude) AS end_longitude'),
                DB::raw('MAX(TIME(DATE_ADD(pbh.device_datetime, INTERVAL 330 MINUTE))) AS end_time'),
                DB::raw('MAX(pbh.odometer) AS end_odometer'),
                DB::raw('FORMAT(MAX(pbh.odometer) - MIN(pbh.odometer), 2) AS odometer_difference')
            )
            ->whereRaw('(DATE_ADD(pbh.device_datetime, INTERVAL 330 MINUTE)) >= ?', [$startDay])
            ->whereRaw('(DATE_ADD(pbh.device_datetime, INTERVAL 330 MINUTE)) <= ?', [$endDay])
            ->when($deviceImei !== 'All', function ($query) use ($deviceImei) {
                if (is_array($deviceImei)) {
                    return $query->whereIn('pbh.device_imei', $deviceImei);
                } else {
                    return $query->where('pbh.device_imei', $deviceImei);
                }
            })
            ->groupBy('pbh.device_imei', 'v.vehicle_name', 'date')
            ->orderBy('pbh.device_imei')
            ->orderBy('date')
            ->get();
        // dd(DB::getQueryLog());
        if ($results->isEmpty()) {
            $response = ["success" => false, "message" => 'No Distance Data Found', "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $tot_kms = 0;
            foreach ($results as $res) {
                $tot_kms =  $tot_kms + $res->odometer_difference;
            }
            $for_tot_kms = number_format((float)$tot_kms, 2, '.', '');
            $response = ["success" => true, "data" => $results, "total_kms" => $for_tot_kms, "status_code" => 200];
            return response()->json($response, 200);
        }
    }
}
