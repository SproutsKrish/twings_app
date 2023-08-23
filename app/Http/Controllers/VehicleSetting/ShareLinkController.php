<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\Controller;
use App\Models\ShareLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShareLinkController extends Controller
{
    public function current_link($id)
    {
        $client_id = auth()->user()->client_id;
        $link_type = "current";

        $data = DB::table('live_data')
            ->select('lattitute', 'longitute')
            ->where('deviceimei', $id)
            ->first();

        if (!$data) {
            $response = ["success" => false, "message" => 'No Data Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $latitude = $data->lattitute; // Replace with your actual latitude column name
        $longitude = $data->longitute; // Replace with your actual longitude column name

        $link = "https://www.google.com/maps?q={$latitude},{$longitude}";

        $links = [
            'client_id' => $client_id,
            'link' => $link,
            'link_type' => $link_type,
        ];

        try {
            ShareLink::create($links);

            $response = ["success" => true, "data" => $link, "status_code" => 200];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Handle the exception and log or print the error
            $response = ["success" => false, "message" => 'Error saving link', "status_code" => 500];
            return response()->json($response, 500);
        }
    }


    public function live_link(Request $request)
    {
        // dd($id);
        // dd($datetime);

        $client_id = auth()->user()->client_id;
        $link_type = "duration";

        $currentDateTime = now()->format('Y-m-d H:i:s');
        // dd($request->datetime);

        $data = DB::table('play_back_histories')
            ->select('*')
            ->where('device_imei', $request->device_imei)
            ->whereBetween('device_datetime', [$currentDateTime,  $request->datetime])
            ->orderBy('device_datetime', 'DESC')
            ->get();

        // dd($data);
        if ($data->isEmpty()) {
            $response = ["success" => false, "message" => 'Link Expired', "status_code" => 404];
            return response()->json($response, 404);
        }

        $waypoints = [];

        foreach ($data as $record) {
            $latitude = $record->latitude;
            $longitude = $record->longitude;
            $waypoints[] = "{$latitude},{$longitude}";
        }

        $waypointsString = implode('|', $waypoints);
        $googleMapsLink = "https://www.google.com/maps/dir/{$waypointsString}";

        $response = ["success" => true, "data" => $googleMapsLink, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function destroy($id)
    {
        $link = ShareLink::find($id);

        if (!$link) {
            $response = ["success" => false, "message" => 'Link Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        if ($link->delete()) {
            $response = ["success" => true, "data" => 'Link Deleted', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Delete Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
