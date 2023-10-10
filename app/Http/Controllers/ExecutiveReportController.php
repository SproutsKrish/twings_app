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
    // public function index(Request $request)
    // {

    //     try {
    //         $start_date = $request->input('start_day');
    //         $end_date = $request->input('end_day');
    //         $device_imei = $request->input('device_imei');

    //         $result = DB::table('play_back_histories as p')
    //         ->join('vehicles as v','v.device_imei','=','p.device_imei')

    //         ->where('p.device_datetime','>=',$start_date)->where('p.device_datetime','<=',$end_date)->where('p.device_imei',$device_imei)
    //         ->select('v.vehicle_name',DB::Raw('DATE(p.device_datetime) as report_date,MIN(p.odometer) as start_odometer,MAX(p.odometer) as end_odometer,ROUND(MAX(p.odometer)-MIN(p.odometer),2) as distance,MIN(p.speed) as min_speed,MAX(speed) as max_speed,ROUND(AVG(p.speed),2) as avg_speed'))->groupBy(DB::raw('DATE(p.device_datetime)'),'report_date','v.vehicle_name')->get();
    //         $response = ["success" => true, "data" => $result, "status_code" => 200];
    //         return response()->json($response, 200);
    //         } catch (\Throwable $th) {
    //         //throw $th;
    //         return response($th,500);
    //     }
    // }

    public function index(Request $request)
    {
        try {
            $start_date = $request->input('start_day');
            $end_date = $request->input('end_day');
            $device_imei = $request->input('device_imei');

            $result = DB::table('executive_reports')
                ->where('report_date', '>=', $start_date)
                ->where('report_date', '<=', $end_date)
                ->where('deviceimei', $device_imei)
                ->select(
                    'id',
                    'vehicle_id',
                    'client_id',
                    'deviceimei',
                    'report_date',
                    'vehicle_name',
                    'start_odometer',
                    'end_odometer',
                    'distance',
                    'avg_speed',
                    'min_speed',
                    'max_speed',
                    'rpm_milege_per_hour',
                    'mileage_per_hour',
                    'start_fuel',
                    'end_fuel',
                    'fuel_fill_litre',
                    'fuel_dip_litre',
                    'fuel_consumed_litre',
                    'mileage',
                    'start_engine_hour_meter',
                    'end_engine_hour_meter',
                    DB::raw('ROUND(end_engine_hour_meter) - ROUND(start_engine_hour_meter) AS total_engine_hour_meter'),
                    DB::raw('SEC_TO_TIME(parking_duration * 60) AS parking_duration'),
                    DB::raw('SEC_TO_TIME(idle_duration * 60) AS idle_duration'),
                    DB::raw('SEC_TO_TIME(moving_duration * 60) AS moving_duration'),
                    DB::raw('SEC_TO_TIME(trip_duration * 60) AS trip_duration'),
                    DB::raw('SEC_TO_TIME(ac_duration * 60) AS ac_duration'),
                    DB::raw('SEC_TO_TIME(total_rpm_duration * 60) AS total_rpm_duration'),
                    DB::raw('SEC_TO_TIME(total_idle_rpm_duration * 60) AS total_idle_rpm_duration'),
                    DB::raw('SEC_TO_TIME(total_normal_rpm_duration * 60) AS total_normal_rpm_duration'),
                    DB::raw('SEC_TO_TIME(total_max_rpm_duration * 60) AS total_max_rpm_duration'),
                    DB::raw('SEC_TO_TIME(drum_left_rotation * 60) AS drum_left_rotation'),
                    DB::raw('SEC_TO_TIME(drum_right_rotation * 60) AS drum_right_rotation'),
                )->get();

            // $result = DB::table('play_back_histories as p')
            // ->join('vehicles as v','v.device_imei','=','p.device_imei')
            // ->join('executive_fuel_reports AS ef','ef.deviceimei','=','p.device_imei')
            // ->where('p.device_datetime','>=',$start_date)
            // ->where('p.device_datetime','<=',$end_date)
            // ->where('p.device_imei',$device_imei)
            // ->select('v.vehicle_name',
            // 'ef.start_fuel','ef.end_fuel','ef.fuel_fill_litre','ef.fuel_dip_litre','ef.fuel_consumed_litre','ef.mileage','ef.rpm_mileage_per_hour','ef.mileage_per_hour',
            // 'report_date AS triptime','report_date AS runningtime','report_date AS runningtime','report_date AS parkingtime_hhmmss','report_date AS ac_time_hhmmss','report_date AS rpm_time_hhmmss',
            // DB::Raw('DATE(p.device_datetime) as report_date,MIN(p.odometer) as start_odometer,MAX(p.odometer) as end_odometer,ROUND(MAX(p.odometer)-MIN(p.odometer),2) as distance,MIN(p.speed) as min_speed,MAX(speed) as max_speed,ROUND(AVG(p.speed),2) as avg_speed'))
            // ->groupBy(DB::raw('DATE(p.device_datetime)')
            // ,'ef.start_fuel','ef.end_fuel','ef.fuel_fill_litre','ef.fuel_dip_litre','ef.fuel_consumed_litre','ef.mileage','ef.rpm_mileage_per_hour','ef.mileage_per_hour'
            // ,'report_date','v.vehicle_name')->get();

            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response($th, 500);
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
