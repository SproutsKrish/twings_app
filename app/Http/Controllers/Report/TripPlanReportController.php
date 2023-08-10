<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\TripPlanReport;
use Illuminate\Http\Request;

class TripPlanReportController extends BaseController
{
    public function trip_plan_report(Request $request)
    {
        $playbackReports = TripPlanReport::whereBetween('created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('vehicleid', $request->input('vehicleid'))
            ->get();

        if ($playbackReports->isEmpty()) {
            return $this->sendError('No Trip Plan Data Found');
        }

        return $this->sendSuccess($playbackReports);
    }
}
