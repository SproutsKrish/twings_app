<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShareLinkController extends Controller
{
    public function current_link($id)
    {
        $data = DB::table('live_data')
            ->select('lattitute', 'longitute')
            ->where('vehicle_id', $id)
            ->first();
        // dd($data);

        $latitude = $data->lattitute; // Replace with your actual latitude
        $longitude = $data->longitute; // Replace with your actual longitude

        $googleMapsLink = "https://www.google.com/maps?q={$latitude},{$longitude}";

        if (empty($googleMapsLink)) {
            $response = ["success" => false, "message" => 'No Link Generated', "status_code" => 404];
            return response()->json($response, 404);
        }
        $response = ["success" => true, "data" => $googleMapsLink, "status_code" => 200];
        return response()->json($response, 200);
    }
}
