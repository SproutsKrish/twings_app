<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaybackReportController extends Controller
{
    public function get_playback_report(Request $request)
    {
        $playbackReports = DB::table('play_back_histories as A')
            ->join('vehicles as B', 'A.device_imei', '=', 'B.device_imei')
            ->whereBetween(DB::raw('DATE_ADD(A.device_datetime, INTERVAL 330 MINUTE)'), [$request->input('start_day'), $request->input('end_day')])
            ->where('A.device_imei', $request->input('deviceimei'))
            ->select('A.id', 'A.device_imei', 'A.latitude', 'A.longitude', 'A.speed', 'A.odometer', 'A.angle', DB::raw("DATE_ADD(A.device_datetime, INTERVAL '330' MINUTE) as device_datetime"), 'A.ignition', 'A.ac_status', 'B.vehicle_name')
            ->orderBy('A.device_datetime')
            ->get();

        $data = DB::table('play_back_histories')
            ->whereBetween(DB::raw('DATE_ADD(device_datetime, INTERVAL 330 MINUTE)'), [$request->input('start_day'), $request->input('end_day')])
            ->where('device_imei', $request->input('deviceimei'))
            ->select(DB::raw("round(max(odometer) - min(odometer), 2) AS total_distance"))
            ->first();

        $total_distance = $data->total_distance;

        if ($playbackReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Playback Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => ['playback' => $playbackReports, 'total_distance' => $total_distance], "status_code" => 200];
        return response($response, 200);
    }
}
