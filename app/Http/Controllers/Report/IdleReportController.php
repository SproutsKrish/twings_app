<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\IdleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdleReportController extends BaseController
{
    public function get_idle_report(Request $request)
    {
        $idleReports = IdleReport::select('idle_reports.*', 'B.vehicle_name') // Specify the correct table name
            ->join('vehicles as B', 'idle_reports.vehicle_id', '=', 'B.id')
            ->where('idle_reports.start_day', '>', $request->input('start_day'))
            ->where('idle_reports.end_day', '<', $request->input('end_day'))
            ->where('idle_reports.vehicle_id', '=', $request->input('vehicle_id'))
            ->get();

        if ($idleReports->isEmpty()) {
            return $this->sendError('No Idle Data Found');
        }


        return $this->sendSuccess($idleReports);
    }
}
