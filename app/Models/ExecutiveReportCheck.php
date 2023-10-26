<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutiveReportCheck extends Model
{
    use HasFactory;

    protected $table = "twings.exceutive_report_check";

    protected $fillable = [
        "start_odometer",
        "end_odometer",
        "distance",
        "avg_speed",
        "min_speed",
        "max_speed",
        "rpm_milege_per_hour",
        "mileage_per_hour",
        "start_fuel",
        "end_fuel",
        "fuel_fill_litre",
        "fuel_dip_litre",
        "fuel_consumed_litre",
        "mileage",
        "start_engine_hour_meter",
        "end_engine_hour_meter",
        "total_engine_hour_meter",
        "parking_duration",
        "idle_duration",
        "moving_duration",
        "trip_duration",
        "ac_duration",
        "total_rpm_duration",
        "total_idle_rpm_duration",
        "total_normal_rpm_duration",
        "total_max_rpm_duration",
        "drum_left_rotation",
        "drum_right_rotation"
    ];
}
