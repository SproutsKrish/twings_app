<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class IdleReportController extends BaseController
{
    public function get_idle_report(Request $request)
    {
        $idleReports = DB::table('idle_reports as A')
            ->join('vehicles as B', 'A.device_imei', '=', 'B.device_imei')
            ->join('twings.vehicle_types as C', 'B.vehicle_type_id', '=', 'C.id')
            ->whereRaw("DATE_ADD(A.start_datetime, INTERVAL 330 MINUTE) >= ?", [$request->input('start_day')])
            ->whereRaw("DATE_ADD(A.end_datetime, INTERVAL 330 MINUTE) <= ?", [$request->input('end_day')])
            ->when($request->input('device_imei') !== 'All', function ($query) use ($request) {
                return $query->where('A.device_imei', '=', $request->input('device_imei'));
            })
            ->select(
                'A.id',
                'A.vehicle_id',
                'A.device_imei',
                'B.vehicle_name',
                'A.start_latitude',
                'A.start_longitude',
                'C.vehicle_type',
                'C.short_name',
                DB::raw("DATE_ADD(A.start_datetime, INTERVAL '330' MINUTE) as start_datetime"),
                DB::raw("DATE_ADD(A.end_datetime, INTERVAL 330 MINUTE) as end_datetime"),
                DB::raw("TIME_FORMAT(TIMEDIFF(A.end_datetime, A.start_datetime), '%H:%i:%s') as idle_duration")
            )
            ->orderBy('A.id', 'desc')
            ->get();

        if ($idleReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Idle Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $idleReports, "status_code" => 200];
        return response($response, 200);
    }
}
