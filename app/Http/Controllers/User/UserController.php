<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ModelHasRole;
use App\Models\Tenant;
use App\Models\User;
use Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
        dd(auth());
        $users = User::all();

        if ($users->isEmpty()) {
            return $this->sendError('No Users Found');
        }

        return $this->sendSuccess($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['secondary_password'] = bcrypt('twingszxc');
        $data['role_id'] = $input['role_id'];

        $role_id = $data['role_id'];
        //unset($input['role_id']);
        $role = Role::find($role_id);
        $permissions = $role->permissions;

        $user = new User($input);

        if ($user->save()) {

            $tenant1 = Tenant::create(['id' => $request->input('name')]);
            $tenant1->domains()->create(['domain' => $request->input('name') . 'localhost']);

            $data['model_type'] = "App\Models\User";
            $data['model_id'] = $user->id;

            $model_has_role = new ModelHasRole($data);
            $model_has_role->save();

            $user->syncPermissions($permissions);

            DB::commit(); // Commit the transaction
            return $this->sendSuccess("User Inserted Successfully");
        } else {
            DB::rollBack(); // Roll back the transaction
            return $this->sendError('Failed to Insert User');
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
}
