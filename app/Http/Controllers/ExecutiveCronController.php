<?php

namespace App\Http\Controllers\Crons;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExecutiveCronController extends Controller
{
    public function GenerateExecutiveReport()
    {
        $indianTimezone = 'Asia/Kolkata';
        $yesterday = Carbon::now($indianTimezone)->subDay();
        $report_date = $yesterday->toDateString();
        $fromd = $report_date . ' 00:00:00';
        $tod = $report_date . ' 23:59:59';
        $active_client_list = DB::table('vehicles')
            ->select('client_id', DB::raw('CONCAT("client_", client_id) as client_db'))
            ->where('client_id', 12964)
            ->where('status', 1)
            ->groupBy('client_id')
            ->orderBy('client_id', 'asc')
            ->get();
        echo "<pre>";
        foreach ($active_client_list as $key => $c_list) {
            $start_time  = microtime(true);
            $client_db = $c_list->client_db;
            $client_id = $c_list->client_id;
            echo "Executing Client DB " . $client_db . "<br>";
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
            $end_time = microtime(true);
            $execution_time = $end_time - $start_time;
            // Convert seconds to minutes and seconds
            $minutes = floor($execution_time / 60);
            $seconds = $execution_time % 60;

            // Print the execution time in minutes and seconds
            echo "$client_db Execution Time: " . $minutes . " minutes and " . $seconds . " seconds";
        }
    }
    function ConsolidateFuelReports()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $fromDateTime = $yesterday . " 00:00:00";
        $toDateTime = $yesterday . " 23:59:59";
        $vehicles = DB::table('vehicles')
            ->select('vehicles.id as vehicleid', 'vehicles.vehicle_name', 'vehicles.device_imei', 'vehicles.client_id', 'advance_device_configuration.device_type_id')
            ->join('advance_device_configuration', 'vehicles.device_imei', '=', 'advance_device_configuration.deviceimei')
            ->where('advance_device_configuration.vehicle_status', 1)
            ->where('advance_device_configuration.devicetype_name', 'Fuel')
            ->get();
        foreach ($vehicles as $vehicle) {
            $enable_fuel_smooth = $vehicle->enable_fuel_smooth;
            $client_id = $vehicle->client_id;
            $fuelData = $this->getFuelData($client_id, $fromDateTime, $toDateTime, $vehicle->device_imei, $enable_fuel_smooth);
            if ($fuelData->count() > 0) {
                $startMeter = $fuelData->first()->odometer;
                $endMeter = $fuelData->last()->odometer;
                $distance = round($endMeter - $startMeter, 2);
                $startFuel = $fuelData->first()->litres;
                $endFuel = $fuelData->last()->litres;
                $fuelFillDip = $this->getFuelFillDip($client_id, $fromDateTime, $toDateTime, $vehicle->deviceimei);
                $fuelFillLitre = round($fuelFillDip->fulfill, 3);
                $fuelDipLitre = round($fuelFillDip->fuldip, 3);
                $fuelLitre = $fuelFillLitre + $fuelDipLitre;
                $fuelLitre = $fuelLitre < 0 ? 0 : round($fuelLitre, 2);
                $consumedFuel = $startFuel + $fuelLitre - $endFuel;
                $consumedFuel = $consumedFuel < 0 ? -1 * $consumedFuel : $consumedFuel;
                $mileage = $consumedFuel != 0 ? round($distance / $consumedFuel, 2) : 0;
                if ($fuelLitre < 0) {
                    $fuelLitre = 0;
                }
                $consumedFuel = round($consumedFuel, 2);
                $mileage = round($mileage, 2);
                if (!empty($startFuel) && !empty($endFuel)) {
                    $fillData = [
                        "start_fuel" => $startFuel,
                        "end_fuel" => $endFuel,
                        "fuel_fill_litre" => $endFuel,
                        "fuel_dip_litre" => $fuelDipLitre,
                        "fuel_consumed_litre" => $consumedFuel,
                        "mileage" => $mileage,
                        "updated_at" => now(),
                    ];
                    DB::table('executive_reports')
                        ->where('device_imei', $vehicle->device_imei) // Replace 'your_unique_column' and $uniqueValue with appropriate values
                        ->where('report_date', $yesterday) // Replace 'your_unique_column' and $uniqueValue with appropriate values
                        ->update($fillData);
                }
            }
        }
    }
    public function GenerateFuelFillDipReport()
    {
        $vehicles = DB::table('vehicles')
            ->select('vehicles.id', 'vehicles.vehicle_name', 'vehicles.device_imei', 'vehicles.client_id', 'advance_device_configuration.device_type_id', 'configurations.fuel_fill_limit', 'configurations.fuel_dip_limit')
            ->join('advance_device_configuration', 'vehicles.device_imei', '=', 'advance_device_configuration.deviceimei')
            ->join('configurations', 'vehicles.client_id', '=', 'configurations.client_id')
            ->where('advance_device_configuration.vehicle_status', 1)
            ->where('advance_device_configuration.devicetype_name', 'Fuel')
            ->get();
        $from_date = now()->subDay()->startOfDay();
        $fueldates = [];
        for ($i = 0; $i < 144; $i++) {
            if ($i == 0) {
                $start_time = $from_date;
            }
            $end_time = $start_time->copy()->addMinutes(10);

            $fueldates[] = [
                'start_time' => $start_time,
                'end_time' => $end_time,
            ];

            $start_time = $end_time;
        }
        foreach ($vehicles as $v_list) {
            $vehicle = $v_list->device_imei;
            $client_id = $v_list->client_id;
            $fuel_fill_minltr = $v_list->fuel_fill_limit;
            $fuel_dip_minltr = -$v_list->fuel_dip_limit;
            $enable_fuel_smooth = $v_list->enable_fuel_smooth;
            foreach ($fueldates as $flist) {
                $FuelData = $this->getFuelData($client_id, $flist['start_time'], $flist['end_time'], $vehicle, $enable_fuel_smooth);
                $fuelstart_data = $FuelData->first();
                $fuelend_data = $FuelData->last();
                if ($FuelData) {
                    $start_fuel = round($fuelstart_data->litres, 1);
                    $end_fuel = round($fuelend_data->litres, 1);
                    $fuel_diff = round($end_fuel - $start_fuel, 1);
                    $lat = $fuelstart_data->lattitude;
                    $lng = $fuelstart_data->longitude;
                    $start_time = $fuelstart_data->device_datetime;
                    $end_time = $fuelend_data->device_datetime;
                    if ($fuel_diff > $fuel_fill_minltr && $start_time > $from_date) {
                        $fill_data = [
                            "deviceimei" => $vehicle,
                            "lattitute" => $lat,
                            "longitute" => $lng,
                            "start_fuel" => $start_fuel,
                            "end_fuel" => $end_fuel,
                            "report_type" => 'Fill',
                            "fuel_difference" => $fuel_diff,
                            "start_time" => $start_time,
                            "end_time" => $end_time,
                            "created_datetime" => $end_time,
                        ];
                        DB::table('fuel_fill_dip_reports')->insert($fill_data);
                    }
                    if ($fuel_diff < $fuel_dip_minltr && $start_time > $from_date) {
                        $fill_data1 = [
                            "deviceimei" => $vehicle,
                            "lattitute" => $lat,
                            "longitute" => $lng,
                            "start_fuel" => $start_fuel,
                            "end_fuel" => $end_fuel,
                            "report_type" => 'Dip',
                            "fuel_difference" => $fuel_diff,
                            "start_time" => $start_time,
                            "end_time" => $end_time,
                            "created_datetime" => $end_time,
                        ];
                        DB::table('fuel_fill_dip_reports')->insert($fill_data1);
                    }
                }
            }
        }
    }
    public function getFuelData($client_id, $start_time, $end_time, $imei, $enable_fuel_smooth)
    {
        $client_db = 'client_' . $client_id;
        $query = DB::table($client_db . '.fuel_status')
            ->select(
                'odometer',
                'lattitude',
                'longitude',
                'speed',
                'device_datetime',
                'packet_type',
                DB::raw("CASE WHEN $enable_fuel_smooth = 1 THEN smooth_fuel_litre ELSE raw_fuel_litre END AS litres")
            )
            ->where('device_imei', $imei)
            ->whereBetween('device_datetime', [$start_time, $end_time])
            ->orderBy('device_datetime', 'asc')
            ->get();

        return $query;
    }
    public function getFuelFillDip($client_id, $from, $to, $imei)
    {
        $client_db = 'client_' . $client_id;
        $query = DB::table($client_db . '.fuel_fill_dip_reports')
            ->select(
                DB::raw('SUM(CASE WHEN fuel_difference > 5 AND report_type = "Fill" THEN fuel_difference ELSE 0 END) as fulfill'),
                DB::raw('SUM(CASE WHEN fuel_difference < 0 AND report_type = "Dip" THEN fuel_difference ELSE 0 END) as fueldip')
            )
            ->where('deviceimei', $imei)
            ->whereBetween('created_datetime', [$from, $to])
            ->first();
        if ($query) {
            return $query;
        } else {
            return false;
        }
    }
}
