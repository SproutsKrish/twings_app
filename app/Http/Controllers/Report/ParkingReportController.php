<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ParkingReport;
use Illuminate\Http\Request;

class ParkingReportController extends BaseController
{
    public function get_parking_report(Request $request)
    {
        $parkingReports = ParkingReport::where('start_day', '=', $request->input('start_day'))
            ->where('end_day', '=', $request->input('end_day'))
            ->where('vehicle_id', '=', $request->input('vehicle_id'))
            ->get();

        if (!$parkingReports->isEmpty()) {
            return $this->sendError('No Parking Data Found');
        }

        return $this->sendSuccess($parkingReports);
    }
}
