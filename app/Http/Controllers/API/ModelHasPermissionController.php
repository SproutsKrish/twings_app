<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Validator;


class ModelHasPermissionController extends BaseController
{
    // Get all permissions for a user
    public function index()
    {
        $usersWithPermissions = User::with('permissions')->get();

        if ($usersWithPermissions->isEmpty()) {
            return $this->sendError('No User Permissions Found');
        }

        return $this->sendSuccess($usersWithPermissions);
    }

    public function show($user_id)
    {
        $userWithPermissions = User::with('permissions')->find($user_id);

        if (!$userWithPermissions) {
            return $this->sendError('User not found');
        }

        if ($userWithPermissions->permissions->isEmpty()) {
            return $this->sendError('No Permissions Found for this User');
        }

        return $this->sendSuccess($userWithPermissions);
    }

    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);

        if ($user->permissions->isEmpty()) {
            return $this->sendError('No Permissions Found for this User');
        }

        $detachedPermissions = $user->permissions()->detach();

        if ($detachedPermissions) {
            return $this->sendSuccess('All permissions removed from the User');
        } else {
            return $this->sendError('Failed to remove permissions from the User');
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user = User::find($request->input('user_id'));

        if ($user) {
            $detachedPermissions = $user->permissions()->detach();

            $permissions = Permission::whereIn('id', $request->input('permissions'))->get();

            $atttachedPermissions = $user->syncPermissions($permissions);

            if ($atttachedPermissions) {
                return $this->sendSuccess('Permissions linked with the User');
            } else {
                return $this->sendError('No Permissions to link with the User');
            }
        } else {
            return $this->sendError('No User Found');
        }
    }
}
