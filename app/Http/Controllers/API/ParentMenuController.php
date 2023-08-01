<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\ParentMenu;


class ParentMenuController extends BaseController
{
    public function index()
    {
        $parent_menus = ParentMenu::all();

        if ($parent_menus->isEmpty()) {
            return $this->sendError('No Parent Menus Found');
        }

        return $this->sendSuccess($parent_menus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|max:255',
            'parent_menu_name' => 'required|max:255',
            'parent_menu_icon' => 'required|max:255',
            'parent_menu_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $parent_menu = new ParentMenu($request->all());
        if ($parent_menu->save()) {
            return $this->sendSuccess("Parent Menu Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Parent Menu');
        }
    }

    public function show($id)
    {
        $parent_menu = ParentMenu::find($id);

        if (!$parent_menu) {
            return $this->sendError('Parent Menu Not Found');
        }

        return $this->sendSuccess($parent_menu);
    }

    public function update(Request $request, $id)
    {
        $parent_menu = ParentMenu::find($id);

        if (!$parent_menu) {
            return $this->sendError('Parent Menu Not Found');
        }

        $validator = Validator::make($request->all(), [
            'module_id' => 'required|max:255',
            'parent_menu_name' => 'required|max:255',
            'parent_menu_icon' => 'required|max:255',
            'parent_menu_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($parent_menu->update($request->all())) {
            return $this->sendSuccess("Parent Menu Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Parent Menu');
        }
    }

    public function destroy(Request $request, $id)
    {
        $parent_menu = ParentMenu::find($id);

        if (!$parent_menu) {
            return $this->sendError('Parent Menu Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $parent_menu->status = 0;
        $parent_menu->deleted_by = $request->deleted_by;
        $parent_menu->save();
        if ($parent_menu->delete()) {
            return $this->sendSuccess('Parent Menu Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Parent Menu');
        }
    }
}
