<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\RoleRights;
use Illuminate\Support\Facades\DB;

class RoleRightsController extends BaseController
{
    public function index()
    {
        $role_rights = RoleRights::all();

        if ($role_rights->isEmpty()) {
            return $this->sendError('No Role Rights Found');
        }

        return $this->sendSuccess($role_rights);
    }

    public function role_rights_list($role_id)
    {
        $roleRights = DB::table('role_rights')
            ->where('role_id', $role_id)
            ->pluck('rights_id');


        $roles = DB::table('roles')
            ->whereIn('id', $roleRights->toArray())
            ->get();

        if ($roles->isEmpty()) {
            return $this->sendError('No Role Rights Found');
        }

        return $this->sendSuccess($roles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|max:255',
            'rights_id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }


        $result = RoleRights::where('role_id', $request->input('role_id'))
            ->where('rights_id', $request->input('rights_id'))
            ->first();

        if (!empty($result)) {
            return $this->sendError("Role Rights Already Exists");
        }

        $role_right = new RoleRights($request->all());
        if ($role_right->save()) {
            return $this->sendSuccess("Role Rights Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Role Rights');
        }
    }

    public function show($id)
    {
        $role_right = RoleRights::find($id);

        if (!$role_right) {
            return $this->sendError('Role Rights Not Found');
        }

        return $this->sendSuccess($role_right);
    }

    public function update(Request $request, $id)
    {
        $role_right = RoleRights::find($id);

        if (!$role_right) {
            return $this->sendError('Role Rights Not Found');
        }

        $validator = Validator::make($request->all(), [
            'role_id' => 'required|max:255',
            'rights_id' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $result = RoleRights::where('role_id', $request->input('role_id'))
            ->where('rights_id', $request->input('rights_id'))
            ->first();

        if (!empty($result)) {
            return $this->sendError("Role Rights Already Exists");
        }


        if ($role_right->update($request->all())) {
            return $this->sendSuccess("Role Rights Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Role Rights');
        }
    }

    public function destroy(Request $request, $id)
    {
        $role_right = RoleRights::find($id);

        if (!$role_right) {
            return $this->sendError('Role Rights Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $role_right->status = 0;
        $role_right->deleted_by = $request->deleted_by;
        $role_right->save();
        if ($role_right->delete()) {
            return $this->sendSuccess('Role Rights Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Role Rights');
        }
    }
}
