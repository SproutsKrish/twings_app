<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Validator;

class RoleHasPermissionController extends BaseController
{
    // Get all permissions for a role
    public function index()
    {
        $rolesWithPermissions = Role::with('permissions')->get();

        if ($rolesWithPermissions->isEmpty()) {
            return $this->sendError('No Role and Permissions Found');
        }

        return $this->sendSuccess($rolesWithPermissions);
    }

    public function show(Role $role)
    {
        $permissions = $role->permissions; // Retrieve all permissions associated with the role

        if ($permissions->isEmpty()) {
            return $this->sendError('No Permissions Found for this Role');
        }

        return $this->sendSuccess($permissions);
    }

    public function destroy(Role $role)
    {
        $permissionsCount = count($role->permissions);

        $role->syncPermissions([]);

        $removedPermissionsCount = $permissionsCount - count($role->permissions);

        if ($removedPermissionsCount > 0) {
            return $this->sendSuccess($removedPermissionsCount . 'Permission(s) removed from the Role');
        } else {
            return $this->sendError('No Permissions to Remove from the Role');
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer',
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $role = Role::findOrFail($request->input('role_id'));

        $detachedPermissions = $role->permissions()->detach();

        $permissions = Permission::whereIn('id', $request->input('permissions'))->get();

        $attachedPermissions = $role->syncPermissions($permissions);



        if ($attachedPermissions) {
            return $this->sendSuccess('Permissions linked with the Role');
        } else {
            return $this->sendError('No Permissions to link with the Role');
        }
    }
}
