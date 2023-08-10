<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\RouteDeviationReport;
use Illuminate\Http\Request;

class RouteDeviationController extends BaseController
{
    public function route_deviation_report(Request $request)
    {
        $playbackReports = RouteDeviationReport::whereBetween('created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('vehicle_imei', $request->input('deviceimei'))
            ->get();

        if ($playbackReports->isEmpty()) {
            return $this->sendError('No Route Deviation Data Found');
        }

        return $this->sendSuccess($playbackReports);
    }
}
