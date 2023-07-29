<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Admin;

class AdminController extends BaseController
{
    public function index()
    {
        $admins = Admin::all();

        if ($admins->isEmpty()) {
            return $this->sendError('No Admins Found');
        }

        return $this->sendSuccess($admins);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_company' => 'required|max:255',
            'admin_name' => 'required|max:255',
            'admin_email' => 'required|max:255',
            'admin_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $admin = new Admin($request->all());
        if ($admin->save()) {
            return $this->sendSuccess("Admin Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Admin');
        }
    }

    public function show($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return $this->sendError('Admin Not Found');
        }

        return $this->sendSuccess($admin);
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return $this->sendError('Admin Not Found');
        }

        $validator = Validator::make($request->all(), [
            'admin_company' => 'required|max:255',
            'admin_name' => 'required|max:255',
            'admin_email' => 'required|max:255',
            'admin_mobile' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($admin->update($request->all())) {
            return $this->sendSuccess("Admin Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Admin');
        }
    }

    public function destroy(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return $this->sendError('Admin Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $admin->status = 0;
        $admin->deleted_by = $request->deleted_by;
        $admin->save();
        if ($admin->delete()) {
            return $this->sendSuccess('Admin Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Admin');
        }
    }
}
