<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Models\PlaybackReport;
use Illuminate\Support\Facades\DB;


class ExecutiveReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        try {
            $start_date = $request->input('start_day');
            $end_date = $request->input('end_day');
            $device_imei = $request->input('device_imei');
            // $result = DB::table('play_back_histories')
            // ->join('vehicles','vehicles.device_imei','=','play_back_histories.device_imei')
            // ->select('vehicles.vehicle_name',DB::Raw())
            // ->groupBy()
            // ->get();
            // $playbackReports = DB::table('play_back_histories as A')
            // ->join('vehicles as B', 'A.device_imei', '=', 'B.device_imei')
            // ->whereBetween(DB::raw('DATE_ADD(A.device_datetime, INTERVAL 330 MINUTE)'), [$request->input('start_day'), $request->input('end_day')])
            // ->where('A.device_imei', $request->input('deviceimei'))
            // ->select('A.id', 'A.device_imei', 'A.latitude', 'A.longitude', 'A.speed', 'A.odometer', 'A.angle', DB::raw("DATE_ADD(A.device_datetime, INTERVAL '330' MINUTE) as device_datetime"), 'A.ignition', 'A.ac_status', 'B.vehicle_name')
            // ->orderBy('A.device_datetime')
            // ->get();
            $result = DB::table('play_back_histories as p')
            ->join('vehicles as v','v.device_imei','=','p.device_imei')
            ->where('p.device_datetime','>=',$start_date)->where('p.device_datetime','<=',$end_date)->where('p.device_imei',$device_imei)
            ->select('v.vehicle_name',DB::Raw('DATE(p.device_datetime) as report_date,MIN(p.odometer) as start_odometer,MAX(p.odometer) as end_odometer,ROUND(MAX(p.odometer)-MIN(p.odometer),2) as distance,MIN(p.speed) as min_speed,MAX(speed) as max_speed,ROUND(AVG(p.speed),2) as avg_speed'))->groupBy(DB::raw('DATE(p.device_datetime)'),'report_date')->get();
            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
            } catch (\Throwable $th) {
            //throw $th;
            return response($th,500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
