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
            $result = DB::table('play_back_histories')->selectRaw('DATE(device_datetime) as report_date,MIN(odometer) as start_odometer,MAX(odometer) as end_odometer,MIN(speed) as min_speed,MAX(speed) as max_speed,ROUND(AVG(speed),2) as avg_speed')->where('device_datetime','>=',$start_date)->where('device_datetime','<=',$end_date)->where('device_imei',$device_imei)->groupBy(DB::raw('DATE(device_datetime)'))->get();
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
