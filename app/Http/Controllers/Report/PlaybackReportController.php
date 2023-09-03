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
            ->select('A.*', 'B.vehicle_name') // You can select specific columns if needed
            ->get();

        if ($playbackReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Playback Data Found', "status_code" => 404];
            return response($response, 404);
        }
        $response = ["success" => true, "data" => $playbackReports, "status_code" => 200];
        return response($response, 200);
    }
}
