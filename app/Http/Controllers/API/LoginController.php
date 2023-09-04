<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PushNotification;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Tenant;

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
                $request->user()->tokens()->where('id', $token_id)->delete();
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
            'user_id' => 'required',
            'client_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        // $mobile_type = $request->input('mobile_type');
        // $mobile_model = $request->input('mobile_model');
        // $application_name = $request->input('application_name');
        // $server_key = $request->input('server_key');
        // $fcm_token = $request->input('fcm_token');
        // $access_token = $request->input('access_token');
        // $user_id = $request->input('user_id');
        // $role_id = $request->input('role_id');

        // $client_id = $request->input('client_id');

        // $data = array(
        //     'mobile_type' => $mobile_type,
        //     'mobile_model' => $mobile_model,
        //     'application_name' => $application_name,
        //     'server_key' => $server_key,
        //     'fcm_token' => $fcm_token,
        //     'access_token' => $access_token,
        //     'user_id' => $user_id,
        //     'role_id' => $role_id,
        //     'client_id' => $client_id
        // );

        $data = new PushNotification($request->all());

        if ($data->save()) {
            $response = ["success" => true, "message" => "Inserted", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Not Insert", "status_code" => 404];
            return response()->json($response, 404);
        }
    }
}
