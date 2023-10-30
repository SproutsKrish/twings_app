<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExecutiveReport;
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

            $result = ExecutiveReport::getFormattedReports($start_date, $end_date, $device_imei);

            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response($th, 500);
        }
    }

    public function executive_summary(Request $request)
    {
        $startDay = $request->input('start_day');
        $endDay = $request->input('end_day');
        $duration = $request->input('report_type');

        if ($request->input('report_type') == 'all') {
            $executiveReports = DB::table('executive_reports')
                ->select('report_date', DB::raw('SUM(parking_duration) as parking_duration'), DB::raw('SUM(idle_duration) as idle_duration'), DB::raw('SUM(moving_duration) as moving_duration'), DB::raw('SUM(distance) as distance'))
                ->whereBetween('report_date', [$startDay, $endDay])
                ->groupBy('report_date')
                ->get();
        } else {
            $executiveReports = DB::table('executive_reports')
                ->select('report_date', DB::raw("SUM($duration) as $duration"))
                ->whereBetween('report_date', [$startDay, $endDay])
                ->groupBy('report_date')
                ->get();
        }

        return response()->json(['success' => true, 'data' =>  $executiveReports]);
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
