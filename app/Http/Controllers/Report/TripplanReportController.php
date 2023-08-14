<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TripplanReport;

class TripplanReportController extends Controller
{
    public function index()
    {
        $tripplanReports = TripplanReport::all();

        if ($tripplanReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Trip Plan Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $tripplanReports, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $tripplanReport = new TripplanReport($request->all());
        if ($tripplanReport->save()) {
            $response = ["success" => true, "data" => "Trip Plan Inserted Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Trip Plan Not Inserted', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function show($id)
    {
        $tripplanReport = TripplanReport::find($id);

        if (!$tripplanReport) {

            $response = ["success" => false, "message" => 'No Trip Plan Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $tripplanReport, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function update(Request $request, $id)
    {
        $tripplanReport = TripplanReport::find($id);

        if (!$tripplanReport) {
            $response = ["success" => false, "message" => 'No Trip Plan Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        if ($tripplanReport->update($request->all())) {
            $response = ["success" => true, "data" => 'Trip Plan Updated Successfully', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Trip Plan Not Updated', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function trip_plan_report(Request $request)
    {
        $tripReports = TripPlanReport::whereBetween('created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('vehicle_id', $request->input('vehicle_id'))
            ->get();

        if ($tripReports->isEmpty()) {
            $response = ["success" => false, "message" => 'No Trip Plan Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $tripReports, "status_code" => 200];
        return response()->json($response, 200);
    }
}
