<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Role;

class RoleController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            return $this->sendError('No Roles Found');
        }

        return $this->sendSuccess($roles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:roles,name',
            'guard_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $role = new Role($request->all());
        if ($role->save()) {
            return $this->sendSuccess("Role Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Role');
        }
    }

    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->sendError('Role Not Found');
        }

        return $this->sendSuccess($role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->sendError('Role Not Found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:roles,name,' . $id,
            'guard_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($role->update($request->all())) {
            return $this->sendSuccess("Role Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Role');
        }
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->sendError('Role Not Found');
        }

        if ($role->delete()) {
            return $this->sendSuccess('Role Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Role');
        }
    }
}
