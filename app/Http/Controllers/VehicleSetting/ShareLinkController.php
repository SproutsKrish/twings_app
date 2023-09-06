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

    public function link_list()
    {
        $sharelinks = DB::table('share_links as a')
            ->select('a.id', 'b.vehicle_name', 'b.device_imei', 'a.link', 'a.expiry_date', 'a.created_at', DB::raw('(CASE WHEN a.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->join('vehicles as b', 'a.device_imei', '=', 'b.device_imei')
            ->where('a.deleted_at', null)
            ->get();

        if ($sharelinks->isEmpty()) {
            $response = ["success" => false, "message" => 'No Shared Links Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $sharelinks, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function link_show($device_imei)
    {
        $sharelinks = DB::table('share_links as a')
            ->select('a.id', 'b.vehicle_name', 'a.link', 'a.expiry_date', 'a.created_at', DB::raw('(CASE WHEN a.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->join('vehicles as b', 'a.vehicle_id', '=', 'b.id')
            ->where('a.deleted_at', null)
            ->where('a.device_imei', $device_imei)
            ->get();

        if ($sharelinks->isEmpty()) {
            $response = ["success" => false, "message" => 'No Shared Links Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $sharelinks, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function link_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_imei' => 'required|max:255',
            'expiry_date' => 'required|max:255',
            'client_id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 404];
            return response()->json($response, 404);
        }

        $device_imei = $request->input('device_imei');
        $client_id = $request->input('client_id');
        $expiry_date = $request->input('expiry_date');
        $link = "https://www.google.com/maps/@28.6068425,77.2964826,14z?entry=ttu";

        $shareLink = new ShareLink();
        $shareLink->device_imei = $device_imei;
        $shareLink->expiry_date = $expiry_date;
        $shareLink->client_id = $client_id;
        $shareLink->link_type = "Duration";

        $shareLink->link = $link;
        $shareLink->save();

        if ($shareLink->save()) {
            $response = ["success" => true, "message" => 'Link Created', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Create Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function destroy($id)
    {
        $link = ShareLink::find($id);

        if (!$link) {
            $response = ["success" => false, "message" => 'Link Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        if ($link->delete()) {
            $response = ["success" => true, "message" => 'Link Deleted', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Delete Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
