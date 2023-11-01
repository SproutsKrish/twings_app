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

    public function link_list()
    {
        $client_id = auth()->user()->client_id;

        $sharelinks = DB::table('share_links as a')
            ->select('a.id', 'b.vehicle_name', 'a.client_id',  'a.client_db_name',  'b.device_imei', 'a.expiry_date', 'a.link', 'a.created_at', DB::raw('(CASE WHEN a.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->join('vehicles as b', 'a.device_imei', '=', 'b.device_imei')
            ->where('a.client_id', $client_id)
            ->where('a.status', 1)
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
        $sharelinks = DB::table('share_links as a')
            ->select('a.id', 'b.vehicle_name', 'a.client_id',  'a.client_db_name',  'b.device_imei', 'a.expiry_date', 'a.link', 'a.created_at', DB::raw('(CASE WHEN a.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->join('vehicles as b', 'a.device_imei', '=', 'b.device_imei')
            ->where('a.id', $id)
            ->where('a.status', 1)
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
            'device_imei' => 'required|max:255',
            'expiry_date' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 404];
            return response()->json($response, 404);
        }

        $client_id = auth()->user()->client_id;
        $client_db_name = 'client_' .  $client_id;
        $device_imei = $request->input('device_imei');
        $expiry_date = $request->input('expiry_date');

        $shareLink = new ShareLink();
        $shareLink->client_id = $client_id;
        $shareLink->client_db_name = $client_db_name;
        $shareLink->device_imei = $device_imei;
        $shareLink->expiry_date = $expiry_date;
        $shareLink->created_by = auth()->user()->id;
        $share_Link = $shareLink->save();

        if ($share_Link) {
            // $id_encrypt = Crypt::encryptString($shareLink->id);
            // $link = "https://gpsapp.in/share_link/" . $id_encrypt;

            $id_encrypt = $shareLink->id;
            // $link = "http://127.0.0.1:8000/share_link/" . $id_encrypt;
            $link = "https://gpsapp.in/share_link/" . $id_encrypt;


            DB::table('share_links')->where('id', $shareLink->id)->update(['link' => $link]);
            $response = ["success" => true, "message" => 'Link Created', "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => 'Failed to Create Link', "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function share_link_list($client_id)
    {
        $sharelinks = DB::table('share_links as a')
            ->select('a.id', 'b.vehicle_name', 'a.client_id',  'a.client_db_name',  'b.device_imei', 'a.expiry_date', 'a.link', 'a.created_at', DB::raw('(CASE WHEN a.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->join('vehicles as b', 'a.device_imei', '=', 'b.device_imei')
            ->where('a.client_id', $client_id)
            ->where('a.status', 1)
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
        $sharelinks = DB::table('share_links as a')
            ->select('a.id', 'b.vehicle_name', 'a.client_id',  'a.client_db_name',  'b.device_imei', 'a.expiry_date', 'a.link', 'a.created_at', DB::raw('(CASE WHEN a.expiry_date > NOW() THEN "live" ELSE "expired" END) as status'))
            ->join('vehicles as b', 'a.device_imei', '=', 'b.device_imei')
            ->where('a.id', $id)
            ->where('a.status', 1)
            ->get();

        if ($sharelinks->isEmpty()) {
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
