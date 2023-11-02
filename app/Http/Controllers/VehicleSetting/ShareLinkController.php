<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\Controller;
use App\Models\ShareLink;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class ShareLinkController extends Controller
{

    public function link_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_imei' => 'required',
            'vehicle_id' => 'required',
            'expiry_date' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 404];
            return response()->json($response, 404);
        }

        $client_id = auth()->user()->client_id;
        $client_db_name = 'client_' .  $client_id;
        $device_imei = $request->input('device_imei');
        $vehicle_id = $request->input('vehicle_id');
        $expiry_date = $request->input('expiry_date');

        $shareLink = new ShareLink();
        $shareLink->client_id = $client_id;
        $shareLink->client_db_name = $client_db_name;
        $shareLink->vehicle_id = $vehicle_id;
        $shareLink->device_imei = $device_imei;
        $shareLink->expiry_date = $expiry_date;
        $shareLink->created_by = auth()->user()->id;
        $share_Link = $shareLink->save();

        if ($share_Link) {
            // $id_encrypt = Crypt::encryptString($shareLink->id);
            // $link = "http://127.0.0.1:8000/share_link/" . $id_encrypt;

            $id_encrypt = $shareLink->id;
            $link = "https://gpsapp.in/share_link/" . $id_encrypt;

            $shareLink->link = $link;
            $shareLink->update();

            $response = ["success" => true, "message" => 'Link Created', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Create Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
    public function link_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 404];
            return response()->json($response, 404);
        }

        $client_id  = auth()->user()->client_id;
        $vehicle_id = $request->input('vehicle_id');

        $sharelinks = ShareLink::join('vehicles as a', 'a.id', '=', 'share_links.vehicle_id')
            ->select('share_links.id', 'share_links.client_id', 'share_links.client_db_name', 'share_links.vehicle_id', 'a.vehicle_name', 'a.device_imei', 'share_links.expiry_date', 'share_links.link', 'share_links.created_at', DB::raw('(CASE WHEN share_links.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->where('share_links.client_id', $client_id)
            ->where('share_links.vehicle_id', $vehicle_id)
            ->where('share_links.status', 1)
            ->get();

        if ($sharelinks->isEmpty()) {
            $response = ["success" => false, "message" => 'No Shared Links Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $sharelinks, "status_code" => 200];
        return response()->json($response, 200);
    }
    public function link_show($id)
    {
        $sharelinks = ShareLink::join('vehicles as a', 'a.id', '=', 'share_links.vehicle_id')
            ->select('share_links.id', 'share_links.client_id', 'share_links.client_db_name', 'share_links.vehicle_id', 'a.vehicle_name', 'a.device_imei', 'share_links.expiry_date', 'share_links.link', 'share_links.created_at', DB::raw('(CASE WHEN share_links.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->where('share_links.id', $id)
            ->where('share_links.status', 1)
            ->first();

        if ($sharelinks === null) {
            $response = ["success" => false, "message" => 'No Shared Links Found', "status_code" => 404];
            return response()->json($response, 404);
        }


        $response = ["success" => true, "data" => $sharelinks, "status_code" => 200];
        return response()->json($response, 200);
    }
    public function link_delete($id)
    {
        $link = ShareLink::find($id);

        if (!$link) {
            $response = ["success" => false, "message" => 'Link Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }
        $link->status = 0;
        $link->deleted_by = auth()->user()->id;
        $link->update();
        if ($link->delete()) {
            $response = ["success" => true, "message" => 'Link Deleted', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Delete Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }



    public function share_link_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_imei' => 'required',
            'vehicle_id' => 'required',
            'expiry_date' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 404];
            return response()->json($response, 404);
        }

        $client_id = auth()->user()->client_id;
        $client_db_name = 'client_' .  $client_id;
        $device_imei = $request->input('device_imei');
        $vehicle_id = $request->input('vehicle_id');
        $expiry_date = $request->input('expiry_date');

        $shareLink = new ShareLink();
        $shareLink->client_id = $client_id;
        $shareLink->client_db_name = $client_db_name;
        $shareLink->vehicle_id = $vehicle_id;
        $shareLink->device_imei = $device_imei;
        $shareLink->expiry_date = $expiry_date;
        $shareLink->created_by = auth()->user()->id;
        $share_Link = $shareLink->save();

        if ($share_Link) {
            // $id_encrypt = Crypt::encryptString($shareLink->id);
            // $link = "http://127.0.0.1:8000/share_link/" . $id_encrypt;

            $id_encrypt = $shareLink->id;
            $link = "https://gpsapp.in/share_link/" . $id_encrypt;

            $shareLink->link = $link;
            $shareLink->update();

            $response = ["success" => true, "message" => 'Link Created', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Create Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
    public function share_link_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 404];
            return response()->json($response, 404);
        }

        $client_id  = auth()->user()->client_id;
        $vehicle_id = $request->input('vehicle_id');

        $sharelinks = ShareLink::join('vehicles as a', 'a.id', '=', 'share_links.vehicle_id')
            ->select('share_links.id', 'share_links.client_id', 'share_links.client_db_name', 'share_links.vehicle_id', 'a.vehicle_name', 'a.device_imei', 'share_links.expiry_date', 'share_links.link', 'share_links.created_at', DB::raw('(CASE WHEN share_links.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->where('share_links.client_id', $client_id)
            ->where('share_links.vehicle_id', $vehicle_id)
            ->where('share_links.status', 1)
            ->get();

        if ($sharelinks->isEmpty()) {
            $response = ["success" => false, "message" => 'No Shared Links Found', "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $sharelinks, "status_code" => 200];
        return response()->json($response, 200);
    }
    public function share_link_show($id)
    {
        $sharelinks = ShareLink::join('vehicles as a', 'a.id', '=', 'share_links.vehicle_id')
            ->select('share_links.id', 'share_links.client_id', 'share_links.client_db_name', 'share_links.vehicle_id', 'a.vehicle_name', 'a.device_imei', 'share_links.expiry_date', 'share_links.link', 'share_links.created_at', DB::raw('(CASE WHEN share_links.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->where('share_links.id', $id)
            ->where('share_links.status', 1)
            ->first();

        if ($sharelinks === null) {
            $response = ["success" => false, "message" => 'No Shared Links Found', "status_code" => 404];
            return response()->json($response, 404);
        }


        $response = ["success" => true, "data" => $sharelinks, "status_code" => 200];
        return response()->json($response, 200);
    }
    public function share_link_delete($id)
    {
        $link = ShareLink::find($id);

        if (!$link) {
            $response = ["success" => false, "message" => 'Link Not Found', "status_code" => 404];
            return response()->json($response, 404);
        }
        $link->status = 0;
        $link->deleted_by = auth()->user()->id;
        $link->update();
        if ($link->delete()) {
            $response = ["success" => true, "message" => 'Link Deleted', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Delete Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
