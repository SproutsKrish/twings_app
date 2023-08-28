<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Country;
use App\Models\CustomerConfiguration;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\ModelHasRole;
use App\Models\RoleRights;
use App\Models\SubDealer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    // function __construct()
    // {
    //     $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
    //     $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    // }

    public function index()
    {

        $users = User::all();

        if ($users->isEmpty()) {
            return $this->sendError('No Users Found');
        }

        return $this->sendSuccess($users);
    }

    public function store(Request $request)
    {
        //Validation Code
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role_id' => 'required',
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        //Check RoleRights
        $session_role_id = auth()->user()->role_id;
        $role_id = $request->input('role_id');

        $result = RoleRights::where('role_id', $session_role_id)
            ->where('rights_id', $role_id)
            ->first();

        if ($result) {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['secondary_password'] = bcrypt('twingszxc');

            //Get Country Info
            $country = Country::find($request->input('country_id'));

            //Set User Data
            $input['admin_id'] = auth()->user()->admin_id;
            $input['distributor_id'] = auth()->user()->distributor_id;
            $input['dealer_id'] = auth()->user()->dealer_id;
            $input['subdealer_id'] = auth()->user()->subdealer_id;
            $input['client_id'] = auth()->user()->client_id;
            $input['vehicle_owner_id'] = auth()->user()->vehicle_owner_id;
            $input['staff_id'] = auth()->user()->staff_id;
            $input['country_id'] = $country->id;
            $input['country_name'] = $country->country_name;
            $input['timezone_name'] = $country->timezone_name;
            $input['timezone_offset'] = $country->timezone_offset;
            $input['timezone_minutes'] = $country->timezone_minutes;
            $input['created_by'] = auth()->user()->id;
            $input['ip_address'] = $request->ip();

            //Save User Data
            $user = new User($input);
            $user_data = $user->save();

            // Link Role User Model_has_Role
            $data['role_id'] = $user->role_id;
            $data['model_type'] = "App\Models\User";
            $data['model_id'] = $user->id;
            $model_has_role = new ModelHasRole($data);
            $model_has_role->save();

            //Get Role based Permissions
            $role = Role::find($user->role_id);
            $permissions = $role->permissions;
            $user->syncPermissions($permissions);

            if ($user) {
                if ($role_id == 2) {
                    $admin = Admin::create(
                        [
                            'admin_name' => $user->name,
                            'admin_email' => $user->email,
                            'user_id' => $user->id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['admin_id' => $admin->id]);
                } else  if ($role_id == 3) {
                    $distributor = Distributor::create(
                        [
                            'distributor_name' => $user->name,
                            'distributor_email' => $user->email,
                            'user_id' => $user->id,
                            'admin_id' => $user->admin_id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['distributor_id' => $distributor->id]);
                } else  if ($role_id == 4) {
                    $dealer = Dealer::create(
                        [
                            'dealer_name' => $user->name,
                            'dealer_email' => $user->email,
                            'user_id' => $user->id,
                            'admin_id' => $user->admin_id,
                            'distributor_id' => $user->distributor_id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['dealer_id' => $dealer->id]);
                } else  if ($role_id == 5) {
                    $subdealer = SubDealer::create(
                        [
                            'subdealer_name' => $user->name,
                            'subdealer_email' => $user->email,
                            'user_id' => $user->id,
                            'admin_id' => $user->admin_id,
                            'distributor_id' => $user->distributor_id,
                            'dealer_id' => $user->dealer_id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['subdealer_id' => $subdealer->id]);
                } else if ($role_id == 6) {
                    $client = Client::create(
                        [
                            'client_name' => $user->name,
                            'client_email' => $user->email,
                            'user_id' => $user->id,
                            'admin_id' => $user->admin_id,
                            'distributor_id' => $user->distributor_id,
                            'dealer_id' => $user->dealer_id,
                            'subdealer_id' => $user->subdealer_id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['client_id' => $client->id]);

                    $tenant = Tenant::create(['id' => $client->id]);

                    $customer_configurations = CustomerConfiguration::create(
                        [
                            'user_id' => $user->id,
                            'client_id' => $client->id,
                            'db_name' => $tenant->tenancy_db_name,
                            'user_name' => $user->name,
                            'password' => $user->password
                        ]
                    );
                    $tenant->domains()->create(['domain' => $user->name . '.' . 'localhost']);


                    $result = CustomerConfiguration::where('client_id', $client->id)
                        ->first();
                    // dd($result);

                    // Specify the dynamic connection configuration
                    $connectionName = $result->db_name;
                    $connectionConfig = [
                        'driver' => 'mysql',
                        'host' => env('DB_HOST'), // Use the environment variable for host
                        'port' => env('DB_PORT'), // Use the environment variable for port
                        'database' => $result->db_name,    // Change this to the actual database name
                        'username' => env('DB_USERNAME'), // Use the environment variable for username
                        'password' => env('DB_PASSWORD'), // Use the environment variable for password
                        // Add any other connection parameters you need
                    ];

                    // Use the dynamic connection configuration to connect to the database
                    Config::set("database.connections.$connectionName", $connectionConfig);
                    DB::purge($connectionName); // Clear the connection cache


                    // dd($user);
                    $userdata = array(
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,
                        'secondary_password' => $user->secondary_password,
                        'role_id' => $user->role_id,
                        'admin_id' => $user->admin_id,
                        'distributor_id' => $user->distributor_id,
                        'dealer_id' => $user->dealer_id,
                        'subdealer_id' => $user->subdealer_id,
                        'client_id' => $client->id,
                        'vehicle_owner_id' => $user->vehicle_owner_id,
                        'staff_id' => $user->staff_id,
                        'country_id' => $user->country_id,
                        'country_name' => $user->country_name,
                        'timezone_name' => $user->timezone_name,
                        'timezone_offset' => $user->timezone_offset,
                        'timezone_minutes' => $user->timezone_minutes,
                        'created_by' => $user->created_by,
                        'ip_address' => $request->ip()
                    );
                    DB::connection($connectionName)->table('users')->insert($userdata);
                }
                DB::commit(); // Commit the transaction

                $response = ["success" => true, "message" => "User Created Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                DB::rollBack(); // Roll back the transaction
                $response = ["success" => false, "message" => "Failed to Create User", "status_code" => 404];
                return response()->json($response, 404);
            }
        } else {
            $response = ["success" => false, "message" => "User Creation Failed. Unauthorize Role Creation", "status_code" => 404];
            return response()->json($response, 403);
        }
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return $this->sendError('User Not Found');
        }

        return $this->sendSuccess($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User Not Found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name,' . $id,
            'email' => 'required|email|unique:users,email,' . $id . ',id',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role_id' => 'required|exists:roles,id', // Add the validation for role_id
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        // Get the role_id from the request
        $role_id = $request->input('role_id');

        $input = $request->except('role_id');

        // Update user record
        if ($user->update($input)) {

            $user->permissions()->detach();

            // Attach the new role permissions
            $role = Role::findOrFail($role_id);
            $user->syncPermissions($role->permissions);

            return $this->sendSuccess("User Updated Successfully");
        } else {
            return $this->sendError('Failed to Update User');
        }
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user->status = 0;
        $user->deleted_by = $request->deleted_by;
        $user->save();
        if ($user->delete()) {
            return $this->sendSuccess('User Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete User');
        }
    }

    public function showdetails(Request $request)
    {
        $user = $request->user();

        // // Load the permissions relationship with the 'name' attribute
        // $user->load('permissions:name');

        // // Modify the collection to exclude the pivot data
        // $user->permissions->map(function ($permission) {
        //     unset($permission->pivot);
        //     return $permission;
        // });

        // Return user data with permissions names
        if (!$user) {
            return $this->sendError('User Not Found');
        }

        return $this->sendSuccess($user);
    }


    public function yourMethod()
    {
        // Retrieve data from the "vehicletype" table in the "default" database connection
        $vehicleTypes = DB::table('vehicles')->get();

        // Specify the dynamic connection configuration
        $connectionName = 'client_1';
        $connectionConfig = [
            'driver' => 'mysql',
            'host' => env('DB_HOST'), // Use the environment variable for host
            'port' => env('DB_PORT'), // Use the environment variable for port
            'database' => 'client_1',     // Change this to the actual database name
            'username' => env('DB_USERNAME'), // Use the environment variable for username
            'password' => env('DB_PASSWORD'), // Use the environment variable for password
            // Add any other connection parameters you need
        ];

        // Use the dynamic connection configuration to connect to the database
        Config::set("database.connections.$connectionName", $connectionConfig);
        DB::purge($connectionName); // Clear the connection cache
        DB::connection($connectionName)->table('vehicles')->insert([
            'vehicle_name' => 'tn',
            'vehicle_make' => 'ff',
        ]);
        $testData = DB::connection($connectionName)->table('vehicles')->get();

        // Your logic here

        dd($testData);

        // Close the dynamic connection and revert to the default connection
        DB::disconnect($connectionName);
    }
}
