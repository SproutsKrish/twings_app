<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\KeyOnKeyOffReport;
use Illuminate\Http\Request;

class KeyOnKeyOffReportController extends BaseController
{
    public function get_keyonoff_report(Request $request)
    {
        $keyonoffReports = KeyOnKeyOffReport::where('start_day', '=', $request->input('start_day'))
            ->where('end_day', '=', $request->input('end_day'))
            ->where('vehicle_id', '=', $request->input('vehicle_id'))
            ->get();

        if ($keyonoffReports->isEmpty()) {
            return $this->sendError('No KeyOnOff Data Found');
        }

        return $this->sendSuccess($keyonoffReports);
    }
}
