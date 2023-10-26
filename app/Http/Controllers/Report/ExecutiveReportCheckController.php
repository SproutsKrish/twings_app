<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ExecutiveReportCheck;
use Illuminate\Http\Request;

class ExecutiveReportCheckController extends Controller
{
    public function executive_report_check_list(Request $request)
    {
        $data = ExecutiveReportCheck::where('user_id', $request->input('user_id'))->first();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function executive_report_check_update(Request $request)
    {
        $data = ExecutiveReportCheck::where('user_id', $request->input('user_id'))->first();

        $data->fill(array_filter($request->only(['start_odometer', 'end_odometer', 'distance', 'avg_speed', 'min_speed', 'max_speed', 'rpm_milege_per_hour', 'mileage_per_hour', 'start_fuel', 'end_fuel', 'fuel_fill_litre', 'fuel_dip_litre', 'fuel_consumed_litre', 'mileage', 'start_engine_hour_meter', 'end_engine_hour_meter', 'total_engine_hour_meter', 'parking_duration', 'idle_duration', 'moving_duration', 'trip_duration', 'ac_duration', 'total_rpm_duration', 'total_idle_rpm_duration', 'total_normal_rpm_duration', 'total_max_rpm_duration', 'drum_left_rotation', 'drum_right_rotation']), function ($value) {
            return $value !== null;
        }));

        $data->update();

        return response()->json(['success' => true, 'message' => 'Filters Updated']);
    }
}
