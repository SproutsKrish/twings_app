<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class KeyOnKeyOffReportController extends Controller
{
    public function get_keyonoff_report(Request $request)
    {
        $startDay = $request->input('start_day');
        $endDay = $request->input('end_day');
        $deviceIMEI = $request->input('device_imei');

        $results = DB::table('play_back_histories as t')
            ->joinSub(function ($query) use ($startDay, $endDay, $deviceIMEI, $request) {
                $query->from('keyoff_keyon_reports')
                    ->select('vehicle_id', DB::raw('TIMEDIFF(end_datetime, start_datetime) as duration'), 'start_datetime', 'end_datetime', 'start_latitude', 'start_longitude', 'end_latitude', 'end_longitude')
                    ->where('start_datetime', '>=', $startDay)
                    ->where('end_datetime', '<=', $endDay)
                    ->when($request->input('device_imei') !== 'All', function ($query) use ($deviceIMEI) {
                        return $query->where('device_imei', '=', $deviceIMEI);
                    });
            }, 'dt', function ($join) {
                $join->on('t.device_datetime', '>=', DB::raw('dt.start_datetime'))
                    ->on('t.device_datetime', '<=', DB::raw('dt.end_datetime'));
            })
            ->join('vehicles as c', 'c.id', '=', 'dt.vehicle_id')
            ->select(
                'dt.vehicle_id',
                DB::raw('MAX(c.vehicle_name) as vehicle_name'),
                'dt.start_datetime',
                'dt.start_latitude',
                'dt.start_longitude',
                'dt.end_datetime',
                'dt.end_latitude',
                'dt.end_longitude',
                DB::raw('FORMAT(MAX(t.speed), 2) AS max_speed'),
                DB::raw('FORMAT(AVG(t.speed), 2) AS avg_speed'),
                DB::raw('FORMAT(MAX(t.odometer) - MIN(t.odometer), 2) AS distance'),
                'dt.duration'
            )
            ->groupBy('dt.vehicle_id', 'dt.duration', 'dt.start_datetime', 'dt.end_datetime', 'dt.start_latitude', 'dt.start_longitude', 'dt.end_latitude', 'dt.end_longitude')
            ->orderBy('dt.start_datetime')
            ->get();

        if ($results->isEmpty()) {
            $response = ["success" => false, "message" => 'No KeyOnOff Data Found', "status_code" => 404];
            return response($response, 404);
        }

        $response = ["success" => true, "data" => $results, "status_code" => 200];
        return response($response, 200);
    }
}
