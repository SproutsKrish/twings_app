<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\KeyOnKeyOffReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeyOnKeyOffReportController extends BaseController
{
    public function get_keyonoff_report(Request $request)
    {
        $keyonoffReports = KeyOnKeyOffReport::select('keyoff_keyon_reports.*', 'B.vehicle_name') // Specify the correct table name
            ->join('vehicles as B', 'keyoff_keyon_reports.vehicle_id', '=', 'B.id')
            ->where('keyoff_keyon_reports.start_day', '>', $request->input('start_day'))
            ->where('keyoff_keyon_reports.end_day', '<', $request->input('end_day'))
            ->where('keyoff_keyon_reports.vehicle_id', '=', $request->input('vehicle_id'))
            ->get();

        if ($keyonoffReports->isEmpty()) {
            return $this->sendError('No KeyOnOff Data Found');
        }

        return $this->sendSuccess($keyonoffReports);
    }
}
