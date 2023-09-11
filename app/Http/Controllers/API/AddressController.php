<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function live_address(Request $request)
    {
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $format = "json";
        $url = "http://69.197.153.82:8080/reverse?lat=$lat&lon=$lng&format=$format";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
        $response = curl_exec($curl);
        curl_close($curl);

        $address_data = json_decode($response, true);

        return response()->json($address_data['display_name']);
    }
}
