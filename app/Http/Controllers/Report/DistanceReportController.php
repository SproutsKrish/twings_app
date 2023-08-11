<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\DistanceReport;

class DistanceReportController extends BaseController
{
    public function get_distance_report(Request $request)
    {
        $distance_reports = DistanceReport::whereBetween('date', [$request->input('start_day'), $request->input('end_day')])
            ->where('vehicle_id', $request->input('vehicle_id'))
            ->get();

        if ($distance_reports->isEmpty()) {
            return $this->sendError('No Distance Data Found');
        }

        return $this->sendSuccess($distance_reports);
    }
}
