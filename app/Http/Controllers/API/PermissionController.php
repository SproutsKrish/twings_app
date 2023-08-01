<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:permission-list|permission-create|permission-edit|permission-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:permission-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            return $this->sendError('No Permissions Found');
        }

        return $this->sendSuccess($permissions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required',
            'parent_menu_id' => 'required',
            'child_menu_id' => 'required',
            'name' => 'required|max:255|unique:permissions,name',
            'guard_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $permission = new Permission($request->all());

        if ($permission->save()) {
            $user = User::find(1);
            $user->givePermissionTo($permission->id); // Assuming the 'name' field holds the name of the permission.

            return $this->sendSuccess("Permission Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Permission');
        }
    }

    public function show($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->sendError('Permission Not Found');
        }

        return $this->sendSuccess($permission);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->sendError('Permission Not Found');
        }

        $validator = Validator::make($request->all(), [
            'module_id' => 'required',
            'parent_menu_id' => 'required',
            'child_menu_id' => 'required',
            'name' => 'required|max:255|unique:permissions,name,' . $id,
            'guard_name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($permission->update($request->all())) {
            return $this->sendSuccess("Permission Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Permission');
        }
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return $this->sendError('Permission Not Found');
        }

        if ($permission->delete()) {
            return $this->sendSuccess('Permission Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Permission');
        }
    }
}
