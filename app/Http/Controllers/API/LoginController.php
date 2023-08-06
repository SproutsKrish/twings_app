<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
                $role_id = $user->role_id;



                if ($role_id != 6) {
                    $data['token'] = $user->createToken('API Token')->plainTextToken;
                    $data['user'] = $user;
                    return $this->sendSuccess($data);
                } else {
                    $data['token'] = $user->createToken('API Token')->plainTextToken;

                    $tenantData = json_decode(Tenant::find($user->name), true);
                    $tenantDbName = $tenantData['tenancy_db_name'];

                    config([
                        'database.connections.mysql.database' => $tenantDbName,
                    ]);

                    DB::purge('mysql');
                    DB::reconnect('mysql');
                    DB::setDefaultConnection('mysql');

                    $request->session()->put('tenant_db_name', $tenantDbName);


                    $data['info'] = \App\Models\User::all();
                    $data['user'] = $user;

                    return $this->sendSuccess($data);
                }
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

    public function logisn(Request $request)
    {
        // Retrieve credentials from the request
        $credentials = $request->only('email', 'password');

        // Identify the tenant based on the user's email
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Retrieve the tenant's database name
        $tenantData = json_decode(Tenant::find($user->name), true);
        $tenantDbName = $tenantData['tenancy_db_name'];

        // Switch the database connection to the tenant's database
        config([
            'database.connections.tenant.database' => $tenantDbName,
            'database.connections.tenant.host' => env('TENANT_DB_HOST', '127.0.0.1'),
            'database.connections.tenant.port' => env('TENANT_DB_PORT', '3306'),
            'database.connections.tenant.username' => env('TENANT_DB_USERNAME', 'forge'),
            'database.connections.tenant.password' => env('TENANT_DB_PASSWORD', ''),
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // Authenticate the user against the tenant's database
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // At this point, the user is authenticated against the tenant's database.
        // Create and save the user in the tenant's database:

        $newUser = new User([
            'name' => '111e', // Replace with the desired name for the new user.
            'email' => '111@example.com', // Replace with the desired email for the new user.
            'password' => bcrypt('new_user_password'), // Replace with the desired password for the new user.
            // Add other user attributes as needed.
        ]);

        // Save the new user in the tenant's database
        $newUser->save();

        $data['token'] = $newUser->createToken('API Token')->plainTextToken;
        $data['user'] = $newUser;
        return $this->sendSuccess($data);
    }
}
