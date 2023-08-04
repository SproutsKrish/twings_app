<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Module;


class ModuleController extends BaseController
{
    public function index()
    {
        $modules = Module::all();

        if ($modules->isEmpty()) {
            return $this->sendError('No Modules Found');
        }

        return $this->sendSuccess($modules);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_name' => 'required|max:255',
            'module_icon' => 'required|max:255',
            'module_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $module = new Module($request->all());
        if ($module->save()) {
            return $this->sendSuccess("Module Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Module');
        }
    }

    public function show($id)
    {
        $module = Module::find($id);

        if (!$module) {
            return $this->sendError('Module Not Found');
        }

        return $this->sendSuccess($module);
    }

    public function update(Request $request, $id)
    {
        $module = Module::find($id);

        if (!$module) {
            return $this->sendError('Module Not Found');
        }

        $validator = Validator::make($request->all(), [
            'module_name' => 'required|max:255',
            'module_icon' => 'required|max:255',
            'module_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($module->update($request->all())) {
            return $this->sendSuccess("Module Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Module');
        }
    }

    public function destroy(Request $request, $id)
    {
        $module = Module::find($id);

        if (!$module) {
            return $this->sendError('Module Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $module->status = 0;
        $module->deleted_by = $request->deleted_by;
        $module->save();
        if ($module->delete()) {
            return $this->sendSuccess('Module Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Module');
        }
    }
}
