<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExecutiveReport extends Model
{
    use HasFactory;

    public static function getFormattedReports($start_date, $end_date, $device_imei)
    {
        return self::where('report_date', '>=', $start_date)
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
                DB::raw('SEC_TO_TIME(drum_right_rotation * 60) AS drum_right_rotation')
            )
            ->get();
    }

    public static function getSmartReports($start_date, $end_date, $device_imei)
    {
        return self::where('report_date', '>=', $start_date)
            ->where('report_date', '<=', $end_date)
            ->where('deviceimei', $device_imei)
            ->select(
                'deviceimei',
                'vehicle_name',
                DB::raw('SUM(distance) AS distance'),
                DB::raw('SUM(avg_speed) AS avg_speed'),
                DB::raw('SUM(min_speed) AS min_speed'),
                DB::raw('SUM(max_speed) AS max_speed'),
                DB::raw('SUM(rpm_milege_per_hour) AS rpm_milege_per_hour'),
                DB::raw('SUM(mileage_per_hour) AS mileage_per_hour'),
                DB::raw('SUM(start_fuel) AS start_fuel'),
                DB::raw('SUM(end_fuel) AS end_fuel'),
                DB::raw('SUM(fuel_fill_litre) AS fuel_fill_litre'),
                DB::raw('SUM(fuel_dip_litre) AS fuel_dip_litre'),
                DB::raw('SUM(fuel_consumed_litre) AS fuel_consumed_litre'),
                DB::raw('SUM(mileage) AS mileage'),
                DB::raw('SUM(start_engine_hour_meter) AS start_engine_hour_meter'),
                DB::raw('SUM(end_engine_hour_meter) AS end_engine_hour_meter'),
                DB::raw('SUM(ROUND(end_engine_hour_meter) - ROUND(start_engine_hour_meter)) AS total_engine_hour_meter'),
                DB::raw('SEC_TO_TIME(SUM(parking_duration * 60)) AS parking_duration'),
                DB::raw('SEC_TO_TIME(SUM(idle_duration * 60)) AS idle_duration'),
                DB::raw('SEC_TO_TIME(SUM(moving_duration * 60)) AS moving_duration'),
                DB::raw('SEC_TO_TIME(SUM(trip_duration * 60)) AS trip_duration'),
                DB::raw('SEC_TO_TIME(SUM(ac_duration * 60)) AS ac_duration'),
                DB::raw('SEC_TO_TIME(SUM(total_rpm_duration * 60)) AS total_rpm_duration'),
                DB::raw('SEC_TO_TIME(SUM(total_idle_rpm_duration * 60)) AS total_idle_rpm_duration'),
                DB::raw('SEC_TO_TIME(SUM(total_normal_rpm_duration * 60)) AS total_normal_rpm_duration'),
                DB::raw('SEC_TO_TIME(SUM(total_max_rpm_duration * 60)) AS total_max_rpm_duration'),
                DB::raw('SEC_TO_TIME(SUM(drum_left_rotation * 60)) AS drum_left_rotation'),
                DB::raw('SEC_TO_TIME(SUM(drum_right_rotation * 60)) AS drum_right_rotation')
            )->groupby('deviceimei', 'vehicle_name')
            ->get();
    }
}
