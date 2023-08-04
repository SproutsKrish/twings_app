<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check email in email column or name column
        $emailOrName = $credentials['email'];
        $user = User::where('email', $emailOrName)
            ->orWhere('name', $emailOrName)
            ->first();

        if ($user) {
            $passwordMatches = Hash::check($credentials['password'], $user->password) || Hash::check($credentials['password'], $user->secondary_password);

            if ($passwordMatches) {
                $data['token'] = $user->createToken('API Token')->plainTextToken;
                $data['user'] = $user;
                return $this->sendSuccess($data);
            } else {
                return $this->sendError('Invalid Login Credentials');
            }
        } else {
            return $this->sendError('Invalid Login Credentials');
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }
        return $this->sendSuccess('Successfully Logged Out');
    }
}
