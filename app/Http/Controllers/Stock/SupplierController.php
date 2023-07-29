<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Supplier;

class SupplierController extends BaseController
{
    public function index()
    {
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            return $this->sendError('No Supplier Found');
        }

        return $this->sendSuccess($suppliers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|max:255',
            'supplier_email' => 'required|max:255',
            'supplier_mobile_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $supplier = new Supplier($request->all());
        if ($supplier->save()) {
            return $this->sendSuccess("Supplier Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Supplier');
        }
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return $this->sendError('Supplier Not Found');
        }

        return $this->sendSuccess($supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return $this->sendError('Supplier Not Found');
        }

        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|max:255',
            'supplier_email' => 'required|max:255',
            'supplier_mobile_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($supplier->update($request->all())) {
            return $this->sendSuccess("Supplier Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Supplier');
        }
    }

    public function destroy(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return $this->sendError('Supplier Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $supplier->status = 0;
        $supplier->deleted_by = $request->deleted_by;
        $supplier->save();
        if ($supplier->delete()) {
            return $this->sendSuccess('Supplier Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Supplier');
        }
    }
}
