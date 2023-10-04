<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\FcmConfiguration;
use App\Models\PushNotification;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Tenant;
use Carbon\Carbon;
use Exception;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $response = ["success" => false, "message" => $validator->errors(), "status_code" => 401];
                return response($response, 401);
            }

            $credentials = $request->only('email', 'password');

            $user = User::where('email', $credentials['email'])
                ->orWhere('name', $credentials['email'])
                ->first();

            if ($user) {
                $passwordMatches = Hash::check($credentials['password'], $user->password) || Hash::check($credentials['password'], $user->secondary_password);

                if ($passwordMatches) {
                    $data['token'] = $user->createToken('API Token')->plainTextToken;
                    $string = $data['token'];
                    $pipePosition = strpos($string, "|");

                    if ($pipePosition !== false) {
                        $token_id = substr($string, 0, $pipePosition);
                    }
                    $data['token_id'] = $token_id;
                    $data['user'] = $user;

                    $response = ["success" => true, "data" => $data, "status_code" => 200];
                    return response($response, 200);
                } else {
                    $response = ["success" => false, "message" => "Password Mismatch", "status_code" => 401];
                    return response($response, 401);
                }
            } else {
                $response = ["success" => false, "message" => 'User Does Not Exist', "status_code" => 401];
                return response($response, 401);
            }
        } catch (Exception $e) {
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 401];
            return response($response, 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $token_id = $request->user()->currentAccessToken()->id;

                $last_used_at = null;
                $tokens = DB::table('personal_access_tokens')->where('id', $token_id)->update(['last_used_at' => $last_used_at]);

                // $request->user()->tokens()->where('id', $token_id)->delete();
            }
            $response = ["success" => true, "message" => 'Successfully Logged Out', "status_code" => 200];
            return response($response, 200);
        } catch (Exception $e) {
            $response = ["success" => false, "message" => $e->getMessage(), "status_code" => 401];
            return response($response, 401);
        }
    }

    public function generate_fcm_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_type' => 'required',
            'mobile_model' => 'required',
            'application_name' => 'required',
            'server_key' => 'required',
            'fcm_token' => 'required',
            'access_token' => 'required',
            'token_id' => 'required',
            'user_id' => 'required',
            'client_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $data = new FcmConfiguration($request->all());

        if ($data->save()) {
            $response = ["success" => true, "message" => "Inserted", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Not Insert", "status_code" => 404];
            return response()->json($response, 404);
        }
    }

    public function about_us()
    {
        $result = DB::table('about_company')->pluck('content');
        $response = ["success" => true, "data" => $result, "status_code" => 200];
        return response()->json($response, 200);
    }
}
