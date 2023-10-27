<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleHistoryController extends Controller
{
    public function vehicle_history_details(Request $request)
    {
        $startTime = $request->input('startTime');
        $endTime = $request->input('endTime');
        $deviceImei = $request->input('deviceImei');

        $idlereports = DB::table('idle_reports')
            ->select([
                'device_imei',
                DB::raw('DATE_ADD(start_datetime, INTERVAL 330 MINUTE) as start_datetime'),
                DB::raw('DATE_ADD(end_datetime, INTERVAL 330 MINUTE) as end_datetime'),
                DB::raw("TIME_FORMAT(TIMEDIFF(end_datetime, start_datetime), '%H:%i:%s') as idle_duration")
            ])
            ->whereRaw('DATE_ADD(start_datetime, INTERVAL 330 MINUTE) >= ?', $startTime)
            ->whereRaw('DATE_ADD(end_datetime, INTERVAL 330 MINUTE) <= ?',  $endTime)
            ->where('device_imei', $deviceImei)
            ->get();

        $sumIdleDuration = $idlereports->sum(function ($report) {
            $duration = explode(':', $report->idle_duration);
            return ($duration[0] * 3600) + ($duration[1] * 60) + $duration[2];
        });

        $parkingreports = DB::table('parking_reports')
            ->select([
                'device_imei',
                DB::raw('DATE_ADD(start_datetime, INTERVAL 330 MINUTE) as start_datetime'),
                DB::raw('DATE_ADD(end_datetime, INTERVAL 330 MINUTE) as end_datetime'),
                DB::raw("TIME_FORMAT(TIMEDIFF(end_datetime, start_datetime), '%H:%i:%s') as parking_duration")
            ])
            ->whereRaw('DATE_ADD(start_datetime, INTERVAL 330 MINUTE) >= ?', $startTime)
            ->whereRaw('DATE_ADD(end_datetime, INTERVAL 330 MINUTE) <= ?',  $endTime)
            ->where('device_imei',  $deviceImei)
            ->get();

        $sumparkingDuration = $parkingreports->sum(function ($report) {
            $duration = explode(':', $report->parking_duration);
            return ($duration[0] * 3600) + ($duration[1] * 60) + $duration[2];
        });

        $acreports = DB::table('ac_reports')
            ->select([
                'device_imei',
                DB::raw('DATE_ADD(start_datetime, INTERVAL 330 MINUTE) as start_datetime'),
                DB::raw('DATE_ADD(end_datetime, INTERVAL 330 MINUTE) as end_datetime'),
                DB::raw("TIME_FORMAT(TIMEDIFF(end_datetime, start_datetime), '%H:%i:%s') as ac_duration")
            ])
            ->whereRaw('DATE_ADD(start_datetime, INTERVAL 330 MINUTE) >= ?', $startTime)
            ->whereRaw('DATE_ADD(end_datetime, INTERVAL 330 MINUTE) <= ?',  $endTime)
            ->where('device_imei',  $deviceImei)
            ->get();

        $sumacDuration = $acreports->sum(function ($report) {
            $duration = explode(':', $report->ac_duration);
            return ($duration[0] * 3600) + ($duration[1] * 60) + $duration[2];
        });

        $playbackhistories = DB::table('play_back_histories')
            ->select([
                DB::raw('MAX(speed) as max_speed'),
                DB::raw('ROUND(AVG(speed), 2) as avg_speed'),
                DB::raw('ROUND(MAX(odometer) - MIN(odometer), 2) as distances')
            ])
            ->whereBetween(
                DB::raw('DATE_ADD(device_datetime, INTERVAL 330 MINUTE)'),
                [$startTime, $endTime]
            )
            ->where('device_imei', $deviceImei)
            ->get();


        return response()->json([
            // 'idlereports' => $idlereports,
            // 'parkingreports' => $parkingreports,
            'sum_idle_duration' => gmdate('H:i:s', $sumIdleDuration),
            'sum_parking_duration' => gmdate('H:i:s', $sumparkingDuration),
            'sum_ac_duration' => gmdate('H:i:s', $sumacDuration),
            'max_speed' => $playbackhistories[0]->max_speed,
            'avg_speed' => $playbackhistories[0]->avg_speed,
            'distances' => $playbackhistories[0]->distances,

            'fuel_fill' => 0,
            'fuel_dip' => 0,
            'consumed' => 0,
            'secondary_engine' => 0,
            'rpm_value' => 0,
        ]);
    }
}
