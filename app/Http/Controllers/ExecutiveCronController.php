<?php

namespace App\Http\Controllers\Crons;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ExecutiveCronController extends Controller
{
    public function GenerateExecutiveReport()
    {
        $yesterday = now()->subDay();
        $report_date = now()->subDay()->toDateString();
        $fromd = $yesterday->startOfDay();
        $tod = $yesterday->endOfDay();
        $active_client_list = DB::table('vehicles')
            ->select('client_id', DB::raw('CONCAT("client_", client_id) as client_db'))
            // ->where('client_id', 12964)
            ->where('status', 1)
            ->groupBy('client_id')
            ->orderBy('client_id', 'asc')
            ->get();
        foreach ($active_client_list as $key => $c_list) {
            $client_db = $c_list->client_db;
            $client_id = $c_list->client_id;
            $sub_query_condition = "end_datetime !='' AND end_datetime<'" . $tod . "' AND flag=2 AND start_datetime BETWEEN '" . $fromd . "' AND '" . $tod . "'";
            $vehicles = DB::table($client_db . '.live_data')
                ->leftJoin($client_db . '.temp_distance_data as tdd', 'live_data.deviceimei', '=', 'tdd.deviceimei')
                ->select('live_data.vehicle_id', 'live_data.client_id', 'live_data.vehicle_name', 'live_data.deviceimei', DB::raw('"' . $report_date . '" as report_date'), 'tdd.start_odometer', 'tdd.end_odometer', 'tdd.distance', 'tdd.avg_speed', 'tdd.min_speed', 'tdd.max_speed')
                ->selectSub(function ($query) use ($client_db, $sub_query_condition) {
                    $query->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_datetime, end_datetime))')
                        ->from($client_db . '.idle_reports')
                        ->whereColumn('device_imei', 'live_data.deviceimei')
                        ->whereRaw($sub_query_condition);
                }, 'idle_duration') // First subquery
                ->selectSub(function ($query) use ($client_db, $sub_query_condition) {
                    $query->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_datetime, end_datetime))')
                        ->from($client_db . '.parking_reports')
                        ->whereColumn('device_imei', 'live_data.deviceimei')
                        ->whereRaw($sub_query_condition);
                }, 'parking_duration') // Second subquery
                ->selectSub(function ($query) use ($client_db, $sub_query_condition) {
                    $query->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_datetime, end_datetime))')
                        ->from($client_db . '.keyoff_keyon_reports')
                        ->whereColumn('device_imei', 'live_data.deviceimei')
                        ->whereRaw($sub_query_condition);
                }, 'trip_duration') // Third subquery
                ->selectSub(function ($query) use ($client_db, $sub_query_condition) {
                    $query->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, start_datetime, end_datetime))')
                        ->from($client_db . '.ac_reports')
                        ->whereColumn('device_imei', 'live_data.deviceimei')
                        ->whereRaw($sub_query_condition);
                }, 'ac_duration')
                // ... Add more subqueries as needed
                ->where('live_data.vehicle_status', 1)
                ->where('live_data.client_id', $client_id)
                ->get()->toArray();
            $VehicleDataInsertArray = json_decode(json_encode($vehicles), true);
            echo "<pre>";
            print_r($VehicleDataInsertArray);
            DB::table($client_db . '.executive_reports')->insert($VehicleDataInsertArray);
        }
    }
}
