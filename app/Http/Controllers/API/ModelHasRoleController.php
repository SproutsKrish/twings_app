<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ModelHasRole;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class ModelHasRoleController extends BaseController
{
    public function index()
    {
        $usersWithRoles = User::with('roles')->get();

        if ($usersWithRoles->isEmpty()) {
            return $this->sendError('No Users with Roles Found');
        }

        return $this->sendSuccess($usersWithRoles);
    }

    public function role_user($user_id)
    {
        $user = User::find($user_id);

        if ($user) {
            $roles = $user->roles;

            if ($roles->isEmpty()) {
                return $this->sendError('No Roles Found for this User');
            }

            $roleNames = $roles->pluck('name');

            return $this->sendSuccess($roleNames);
        } else {
            return $this->sendError('No Users Found');
        }
    }

    public function users_role($role_id)
    {
        $role = Role::findOrFail($role_id);

        $usersWithRole = $role->users;

        if ($usersWithRole->isEmpty()) {
            return $this->sendError('No Users Found for this Role');
        }

        return $this->sendSuccess($usersWithRole);
    }

    public function user_role_update(Request $request, $id)
    {
        $user = User::find($id);
        $user->permissions()->detach();
        $role = Role::findOrFail($request->input('role_id'));
        $user->syncPermissions($role->permissions);

        if ($user) {
            $role_id = $request->input('role_id');
            $user_id = $id;
            $data = DB::table('model_has_roles')
                ->where('model_id', $user_id)
                ->first();

            if ($data) {
                $old_role_id = $data->role_id;
                if ($old_role_id == $role_id) {
                    return $this->sendSuccess("User aaaa Successfully");
                } else {
                    DB::table('model_has_roles')
                        ->where('model_id', $user_id)
                        ->update(['role_id' => $role_id]);
                    return $this->sendSuccess("User bbbb Successfully");
                }
            } else {
                $data['role_id'] = $request->input('role_id');
                $data['model_type'] = "App\Models\User";
                $data['model_id'] = $user_id;

                $model_has_role = new ModelHasRole($data);
                $model_has_role->save();

                return $this->sendSuccess("User cc Successfully");
            }
        } else {
            return $this->sendError('No Users Found');
        }
    }
}
