<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\IdleReport;
use Illuminate\Http\Request;

class IdleReportController extends BaseController
{
    public function get_idle_report(Request $request)
    {
        $idleReports = IdleReport::where('start_day', '=', $request->input('start_day'))
            ->where('end_day', '=', $request->input('end_day'))
            ->where('vehicle_id', '=', $request->input('vehicle_id'))
            ->get();

        if ($idleReports->isEmpty()) {
            return $this->sendError('No Idle Data Found');
        }

        return $this->sendSuccess($idleReports);
    }
}
