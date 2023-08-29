<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

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
                ->orWhere('mobile_no', $credentials['email'])
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
}
