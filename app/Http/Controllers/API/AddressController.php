<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        if (empty($address_data['display_name'])) {
            $this->get_address($lat, $lng);
        } else {
            $response = ["success" => true, "data" => $address_data['display_name'], "status_code" => 200];
            return response()->json($response, 200);
        }
    }

    public function get_address($lat, $lng)
    {
        $format = "json";
        $url = "http://69.197.153.82:8080/reverse?lat=$lat&lon=$lng&format=$format";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.9'));
        $response = curl_exec($curl);
        curl_close($curl);

        return $address_data = json_decode($response, true);
    }

    // public function logo(Request $request)
    // {
    //     //Validation Code
    //     $validator = Validator::make($request->all(), [
    //         'country_name' => 'required',
    //         'short_name' => 'required',
    //         'phone_code' => 'required',
    //         'timezone_name' => 'required',
    //         'timezone_offset' => 'required',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    //     ]);

    //     if ($validator->fails()) {
    //         $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
    //         return response()->json($response, 403);
    //     }

    //     $fileName = time() . '.' . $request->image->extension();
    //     $request->image->storeAs('public/images', $fileName);

    //     $user = new Country();
    //     $user->country_name = $request->input('country_name');
    //     $user->short_name = $request->input('short_name');
    //     $user->phone_code = $request->input('phone_code');
    //     $user->timezone_name = $request->input('timezone_name');
    //     $user->timezone_offset = $request->input('timezone_offset');
    //     $user->timezone_minutes = $fileName;
    //     $user->save();
    // }
}
