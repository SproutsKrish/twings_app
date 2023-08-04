<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\ChildMenu;

class ChildMenuController extends BaseController
{
    public function index()
    {
        $child_menus = ChildMenu::all();

        if ($child_menus->isEmpty()) {
            return $this->sendError('No Child Menus Found');
        }

        return $this->sendSuccess($child_menus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_menu_id' => 'required|max:255',
            'child_menu_name' => 'required|max:255',
            'child_menu_icon' => 'required|max:255',
            'child_menu_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $child_menu = new ChildMenu($request->all());
        if ($child_menu->save()) {
            return $this->sendSuccess("Child Menu Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Child Menu');
        }
    }

    public function show($id)
    {
        $child_menu = ChildMenu::find($id);

        if (!$child_menu) {
            return $this->sendError('Child Menu Not Found');
        }

        return $this->sendSuccess($child_menu);
    }

    public function update(Request $request, $id)
    {
        $child_menu = ChildMenu::find($id);

        if (!$child_menu) {
            return $this->sendError('Child Menu Not Found');
        }

        $validator = Validator::make($request->all(), [
            'parent_menu_id' => 'required|max:255',
            'child_menu_name' => 'required|max:255',
            'child_menu_icon' => 'required|max:255',
            'child_menu_url' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($child_menu->update($request->all())) {
            return $this->sendSuccess("Child Menu Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Child Menu');
        }
    }

    public function destroy(Request $request, $id)
    {
        $child_menu = ChildMenu::find($id);

        if (!$child_menu) {
            return $this->sendError('Child Menu Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $child_menu->status = 0;
        $child_menu->deleted_by = $request->deleted_by;
        $child_menu->save();
        if ($child_menu->delete()) {
            return $this->sendSuccess('Child Menu Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Child Menu');
        }
    }
}
