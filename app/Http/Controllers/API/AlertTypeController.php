<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AlertType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertTypeController extends Controller
{
    public function get_alert_list()
    {
        $alertTypes = DB::table('alert_types')
            ->where('status', 1)
            ->get();

        if (empty($alertTypes)) {
            $response = ["success" => false, "message" => "No Datas Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $alertTypes, "status_code" => 200];
            return response()->json($response, 200);
        }
    }
}
