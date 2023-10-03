<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Admin;
use App\Models\AlertType;
use App\Models\Client;
use App\Models\Country;
use App\Models\CustomerConfiguration;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\ModelHasRole;
use App\Models\RoleRights;
use App\Models\Staff;
use App\Models\SubDealer;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserDomain;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function role_based_user_list(Request $request)
    {
        $user_id = $request->input('user_id');

        $data = User::where('id', $user_id)->first();

        if (empty($data)) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
            return response()->json($response, 404);
        }
        $role_id = $data->role_id;

        $user_list = $subdealer_list = [];

        switch ($role_id) {
            case $role_id == 1:
                $user_list =  DB::table('users')
                    ->select('id', 'name', 'email', 'role_id')
                    ->where('role_id', 2)
                    ->get();
                break;
            case $role_id == 2:
                $data = User::find($user_id);
                $admin_id  = $data->admin_id;
                $user_list = DB::table('users')
                    ->select('id', 'name', 'email', 'role_id')
                    ->where('role_id', 3)
                    ->where('admin_id', $admin_id)
                    ->get();
                break;
            case $role_id == 3:
                $data = User::find($user_id);
                $distributor_id  = $data->distributor_id;
                $user_list = DB::table('users')
                    ->select('id', 'name', 'email', 'role_id')
                    ->where('role_id', 4)
                    ->where('distributor_id', $distributor_id)
                    ->get();
                break;
            case $role_id == 4:
                $data = User::find($user_id);
                $dealer_id  = $data->dealer_id;

                $user_list = DB::table('users')
                    ->select('id', 'name', 'email', 'role_id')
                    ->where('role_id', 6)
                    ->where('dealer_id', $dealer_id)
                    ->where('subdealer_id', null)

                    ->get();

                $subdealer_list = DB::table('users')
                    ->select('id', 'name', 'email', 'role_id')
                    ->where('role_id', 5)
                    ->where('dealer_id', $dealer_id)
                    ->get();
                break;

            case $role_id == 5:
                $data = User::find($user_id);
                $subdealer_id  = $data->subdealer_id;
                $user_list = DB::table('users')
                    ->select('id', 'name', 'email', 'role_id')
                    ->where('role_id', 6)
                    ->where('subdealer_id', $subdealer_id)
                    ->get();
                break;

            default:
                $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
                return response()->json($response, 404);
        }

        $result = ['user_list' => $user_list, 'subdealer_list' => $subdealer_list];

        if (empty($result['user_list']) && empty($result['subdealer_list'])) {
            $response = ["success" => false, "message" => "No Datas Found", "status_code" => 404];
            return response()->json($response, 404);
        } else {
            $response = ["success" => true, "data" => $result, "status_code" => 200];
            return response()->json($response, 200);
        }
    }

    public function user_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $role_id = $request->input('role_id');

        if ($role_id == 1) {
            $result =  DB::table('users as a')
                ->join('roles as b', 'a.role_id', '=', 'b.id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.password',
                    'a.mobile_no',
                    'a.country_id',
                    'a.country_name',
                    'a.role_id',
                    'b.name as role'
                )
                ->where('a.role_id', '>', '1')
                ->where('a.status', '1')
                ->orderBy('a.id', 'desc')
                ->get();
        } else if ($role_id == 2) {
            $data = User::find($user_id);
            $admin_id  = $data->admin_id;
            $result =  DB::table('users as a')
                ->join('roles as b', 'a.role_id', '=', 'b.id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.password',
                    'a.mobile_no',
                    'a.country_id',
                    'a.country_name',
                    'a.role_id',
                    'b.name as role'
                )
                ->where('a.role_id', '>', '2')
                ->where('a.status', '1')
                ->where('a.admin_id', '=', $admin_id)
                ->orderBy('a.id', 'desc')
                ->get();
        } else if ($role_id == 3) {
            $data = User::find($user_id);
            $distributor_id  = $data->distributor_id;
            $result =  DB::table('users as a')
                ->join('roles as b', 'a.role_id', '=', 'b.id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.password',
                    'a.mobile_no',
                    'a.country_id',
                    'a.country_name',
                    'a.role_id',
                    'b.name as role'
                )
                ->where('a.role_id', '>', '3')
                ->where('a.status', '1')
                ->where('a.distributor_id', '=', $distributor_id)
                ->orderBy('a.id', 'desc')
                ->get();
        } else if ($role_id == 4) {
            $data = User::find($user_id);
            $dealer_id  = $data->dealer_id;
            $result =  DB::table('users as a')
                ->join('roles as b', 'a.role_id', '=', 'b.id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.password',
                    'a.mobile_no',
                    'a.country_id',
                    'a.country_name',
                    'a.role_id',
                    'b.name as role'
                )
                ->where('a.role_id', '>', '4')
                ->where('a.status', '1')
                ->where('a.dealer_id', '=', $dealer_id)
                ->orderBy('a.id', 'desc')
                ->get();
        } else if ($role_id == 5) {
            $data = User::find($user_id);
            $subdealer_id  = $data->subdealer_id;
            $result =  DB::table('users as a')
                ->join('roles as b', 'a.role_id', '=', 'b.id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.password',
                    'a.mobile_no',
                    'a.country_id',
                    'a.country_name',
                    'a.role_id',
                    'b.name as role'
                )
                ->where('a.role_id', '>', '5')
                ->where('a.status', '1')
                ->where('a.subdealer_id', '=', $subdealer_id)
                ->orderBy('a.id', 'desc')
                ->get();
        } else if ($role_id == 6) {
            $data = User::find($user_id);
            $client_id  = $data->client_id;
            $result =  DB::table('users as a')
                ->join('roles as b', 'a.role_id', '=', 'b.id')
                ->select(
                    'a.id',
                    'a.name',
                    'a.email',
                    'a.password',
                    'a.mobile_no',
                    'a.country_id',
                    'a.country_name',
                    'a.role_id',
                    'b.name as role'
                )
                ->where('a.role_id', '>', '6')
                ->where('a.status', '1')
                ->where('a.client_id', '=', $client_id)
                ->orderBy('a.id', 'desc')
                ->get();
        }

        if (empty($result)) {
            $response = ["success" => false, "message" => "No Users Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $result, "status_code" => 200];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        //Validation Code
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role_id' => 'required',
            'country_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $role_id = $request->input('role_id');
        $result = true;
        if ($result) {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['secondary_password'] = bcrypt('twingszxc');

            //Get Country Info
            $country = Country::find($request->input('country_id'));

            //Set User Data
            $requestKeys = collect($request->all())->keys();

            $input['admin_id'] = auth()->user()->admin_id;
            $input['distributor_id'] = auth()->user()->distributor_id;
            $input['dealer_id'] = auth()->user()->dealer_id;
            $input['subdealer_id'] = auth()->user()->subdealer_id;

            if ($requestKeys->contains('admin_id')) {
                $admin_id = User::find($request->input('admin_id'));
                $input['admin_id']  = $admin_id->admin_id;
            }
            if ($requestKeys->contains('distributor_id')) {
                $distributor_id = User::find($request->input('distributor_id'));
                $input['distributor_id']  = $distributor_id->distributor_id;
            }
            if ($requestKeys->contains('dealer_id')) {
                $dealer_id = User::find($request->input('dealer_id'));
                $input['dealer_id']  = $dealer_id->dealer_id;
            }
            if ($requestKeys->contains('subdealer_id')) {
                $subdealer_id = User::find($request->input('subdealer_id'));
                $input['subdealer_id']  = $subdealer_id->subdealer_id;
            }

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

            // return response()->json($input);

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
                            'admin_mobile' => $user->mobile_no,
                            'admin_address' => $request->input('address'),
                            'user_id' => $user->id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['admin_id' => $admin->id]);
                } else if ($role_id == 3) {
                    $distributor = Distributor::create(
                        [
                            'distributor_name' => $user->name,
                            'distributor_email' => $user->email,
                            'distributor_mobile' => $user->mobile_no,
                            'distributor_address' => $request->input('address'),
                            'user_id' => $user->id,
                            'admin_id' => $user->admin_id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['distributor_id' => $distributor->id]);
                } else if ($role_id == 4) {
                    $dealer = Dealer::create(
                        [
                            'dealer_name' => $user->name,
                            'dealer_email' => $user->email,
                            'dealer_mobile' => $user->mobile_no,
                            'dealer_address' => $request->input('address'),
                            'user_id' => $user->id,
                            'admin_id' => $user->admin_id,
                            'distributor_id' => $user->distributor_id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['dealer_id' => $dealer->id]);
                } else if ($role_id == 5) {
                    $subdealer = SubDealer::create(
                        [
                            'subdealer_name' => $user->name,
                            'subdealer_email' => $user->email,
                            'subdealer_mobile' => $user->mobile_no,
                            'subdealer_address' => $request->input('address'),
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
                            'client_mobile' => $user->mobile_no,
                            'client_address' => $request->input('address'),
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


                    $alert_types =  DB::table('alert_types')
                        ->where('status', '1')
                        ->select('id')
                        ->get();

                    foreach ($alert_types as $alert_type) {
                        $userdata = array(
                            'user_id' => $user->id,
                            'client_id' => $client->id,
                            'alert_type_id' => $alert_type->id,
                            'user_status' => 0,
                            'active_status' => 1,
                        );
                        DB::table('alert_notifications')->insert($userdata);
                    }

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
                        'database' => $result->db_name,   // Change this to the actual database name
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
                        'mobile_no' => $user->mobile_no,
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
                } else if ($role_id == 8) {
                    $staff = Staff::create(
                        [
                            'staff_name' => $user->name,
                            'staff_address' => $request->input('address'),
                            'user_id' => $user->id,
                            'created_by' => auth()->user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );

                    User::where('id', $user->id)
                        ->update(['staff_id' => $staff->id]);
                }

                DB::commit(); // Commit the transaction

                $response = ["success" => true, "message" => "User Created Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                DB::rollBack(); // Roll back the transaction
                $response = ["success" => false, "message" => "Failed to Create User", "status_code" => 403];
                return response()->json($response, 403);
            }
        } else {
            $response = ["success" => false, "message" => "User Creation Failed. Unauthorize Role Creation", "status_code" => 403];
            return response()->json($response, 403);
        }
    }

    public function update(Request $request)
    {
        $user = User::find($request->input('id'));

        if (!$user) {
            $response = ["success" => false, "message" => "User Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->input('id') . 'id',
            'mobile_no' => 'required',
            'role_id' => 'required|exists:roles,id',
            'country_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        if ($user) {
            $input = $request->all();

            //Get Country Info
            $country = Country::find($request->input('country_id'));

            //Set User Data
            $input['country_id'] = $country->id;
            $input['country_name'] = $country->country_name;
            $input['timezone_name'] = $country->timezone_name;
            $input['timezone_offset'] = $country->timezone_offset;
            $input['timezone_minutes'] = $country->timezone_minutes;
            $input['updated_by'] = auth()->user()->id;
            $input['ip_address'] = $request->ip();

            $user_data = $user->update($input);

            if ($user_data) {
                $role_id = $user->role_id;

                if ($role_id == 2) {
                    DB::table('admins')
                        ->where('id', $user->admin_id)
                        ->update([
                            'admin_name' => $request->input('name'),
                            'admin_email' => $request->input('email')
                        ]);
                } else if ($role_id == 3) {
                    DB::table('distributors')
                        ->where('id', $user->distributor_id)
                        ->update([
                            'distributor_name' => $request->input('name'),
                            'distributor_email' => $request->input('email')
                        ]);
                } else if ($role_id == 4) {
                    DB::table('dealers')
                        ->where('id', $user->dealer_id)
                        ->update([
                            'dealer_name' => $request->input('name'),
                            'dealer_email' => $request->input('email')
                        ]);
                } else if ($role_id == 5) {
                    DB::table('sub_dealers')
                        ->where('id', $user->subdealer_id)
                        ->update([
                            'subdealer_name' => $request->input('name'),
                            'subdealer_email' => $request->input('email')
                        ]);
                } else if ($role_id == 6) {

                    DB::table('clients')
                        ->where('id', $user->client_id)
                        ->update([
                            'client_name' => $request->input('name'),
                            'client_email' => $request->input('email')
                        ]);


                    $result = CustomerConfiguration::where('client_id', $user->client_id)
                        ->first();


                    // Specify the dynamic connection configuration
                    $connectionName = $result->db_name;
                    $connectionConfig = [
                        'driver' => 'mysql',
                        'host' => env('DB_HOST'), // Use the environment variable for host
                        'port' => env('DB_PORT'), // Use the environment variable for port
                        'database' => $result->db_name,   // Change this to the actual database name
                        'username' => env('DB_USERNAME'), // Use the environment variable for username
                        'password' => env('DB_PASSWORD'), // Use the environment variable for password
                        // Add any other connection parameters you need
                    ];

                    // Use the dynamic connection configuration to connect to the database
                    Config::set("database.connections.$connectionName", $connectionConfig);
                    DB::purge($connectionName); // Clear the connection cache
                    // dd($user);

                    $userdata = array(
                        'name' => $user->name,
                        'email' => $user->email,
                        'mobile_no' => $user->mobile_no,
                        'password' => $user->password,
                        'secondary_password' => $user->secondary_password,
                        'country_id' => $user->country_id,
                        'country_name' => $user->country_name,
                        'timezone_name' => $user->timezone_name,
                        'timezone_offset' => $user->timezone_offset,
                        'timezone_minutes' => $user->timezone_minutes,
                        'updated_by' => $user->updated_by,
                        'ip_address' => $request->ip()
                    );
                    DB::connection($connectionName)->table('users')->where('id', $user->id)->update($userdata);
                } else if ($role_id == 8) {
                    DB::table('staffs')
                        ->where('id', $user->staff_id)
                        ->update(
                            ['staff_address' => $user->address]
                        );
                }

                DB::commit(); // Commit the transaction

                $response = ["success" => true, "message" => "User Created Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                DB::rollBack(); // Roll back the transaction
                $response = ["success" => false, "message" => "Failed to Create User", "status_code" => 403];
                return response()->json($response, 403);
            }
        } else {
            $response = ["success" => false, "message" => "User Creation Failed. Unauthorize Role Creation", "status_code" => 403];
            return response()->json($response, 403);
        }
    }

    public function destroy(Request $request)
    {
        $user = User::find($request->input('id'));

        if (!$user) {
            $response = ["success" => false, "message" => "User Not Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $user->status = 0;
        $user->deleted_by = $request->input('user_id');
        $user->save();
        if ($user->delete()) {
            $response = ["success" => true, "message" => "User Deleted Successfully", "status_code" => 200];
            return response()->json($response, 200);
        } else {
            $response = ["success" => false, "message" => "Failed To Delete User", "status_code" => 404];
            return response()->json($response, 404);
        }
    }




    public function user_point_list(Request $request)
    {
        $user_id = $request->input('user_id');
        $data = User::where('id', $user_id)->first();

        if (empty($data)) {
            $response = ["success" => false, "message" => "No Data Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $role_id = $data->role_id;

        if ($role_id == 1) {
            $role_id  = $role_id + 1;

            $user_point_list = User::select('name', 'admin_id as id')
                ->where('role_id', $role_id)
                ->get();
        } else if ($role_id == 2) {
            $role_id  = $role_id + 1;

            $data = User::find($user_id);
            $admin_id  = $data->admin_id;

            $user_point_list = User::select('name', 'distributor_id as id')
                ->where('admin_id', $admin_id)
                ->where('role_id', $role_id)
                ->get();
        } else if ($role_id == 3) {
            $role_id  = $role_id + 1;

            $data = User::find($user_id);
            $distributor_id  = $data->distributor_id;

            $user_point_list = User::select('name', 'dealer_id as id')
                ->where('distributor_id', $distributor_id)
                ->where('role_id', $role_id)
                ->get();
        } else if ($role_id == 4) {
            $role_id  = $role_id + 1;

            $data = User::find($user_id);
            $dealer_id  = $data->dealer_id;

            $user_point_list = User::select('name', 'subdealer_id as id')
                ->where('dealer_id', $dealer_id)
                ->where('role_id', $role_id)
                ->get();
        }

        if ($user_point_list->isEmpty()) {
            $response = ["success" => false, "message" => "No Users Found", "status_code" => 404];
            return response()->json($response, 404);
        }

        $response = ["success" => true, "data" => $user_point_list, "status_code" => 200];
        return response()->json($response, 200);
    }


    public function show($id)
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return $this->sendError('User Not Found');
        }

        return $this->sendSuccess($user);
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
            'database' => 'client_1',    // Change this to the actual database name
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


    public function change_user_password(Request $request)
    {
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $user_id = $request->input('user_id');

        if (!Hash::check($old_password, auth()->user()->password)) {
            $response = ["success" => false, "message" => "Current Password is not Correct", "status_code" => 401];
            return response()->json($response, 401);
        } else {
            $new_password = $request->input('new_password');
            // dd(auth()->user()->id);
            $data = User::whereId($user_id)->update([
                'password' => Hash::make($new_password)
            ]);
            if ($data) {

                $last_used_at = Carbon::now();

                $tokens = DB::table('personal_access_tokens')->where('tokenable_id', $user_id)->update(['last_used_at' => $last_used_at]);

                $response = ["success" => true, "message" => "Password Changed Successfully", "status_code" => 200];
                return response()->json($response, 200);
            } else {
                $response = ["success" => false, "message" => "Current Password is not Correct", "status_code" => 404];
                return response()->json($response, 404);
            }
        }
    }
}
