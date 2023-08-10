<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ParkingReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkingReportController extends BaseController
{
    public function get_parking_report(Request $request)
    {
        $parkingReports = ParkingReport::select('parking_reports.*', 'B.vehicle_name') // Specify the correct table name
            ->join('vehicles as B', 'parking_reports.vehicle_id', '=', 'B.id')
            ->where('parking_reports.start_datetime', '>', $request->input('start_day'))
            ->where('parking_reports.end_datetime', '<', $request->input('end_day'))
            ->where('parking_reports.vehicle_id', '=', $request->input('vehicle_id'))
            ->get();

        if ($parkingReports->isEmpty()) {
            return $this->sendError('No Parking Data Found');
        }

        return $this->sendSuccess($parkingReports);
    }
}
