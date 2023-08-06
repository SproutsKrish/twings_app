<?php

namespace App\Http\Controllers\License;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Package;

class PackageController extends BaseController
{
    public function index()
    {
        $packages = Package::all();

        if ($packages->isEmpty()) {
            return $this->sendError('No Packages Found');
        }

        return $this->sendSuccess($packages);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'features_name' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        // Convert the array to a single string with hyphens
        $packageNames = implode(' - ', $request->input('features_name'));

        // dd($packageNames);

        $result = Package::where('package_name', $packageNames)
            ->first();

        // dd($result);
        if ($result == null) {
            $package = new Package();
            $package->package_code = $packageNames;
            $package->package_name = $packageNames;

            if ($package->save()) {
                return $this->sendSuccess("Package Inserted Successfully");
            } else {
                return $this->sendError('Failed to Insert Package');
            }
        } else {
            return $this->sendError('This Package Already Created');
        }
    }

    public function show($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return $this->sendError('Package Not Found');
        }

        return $this->sendSuccess($package);
    }

    public function update(Request $request, $id)
    {
        $package = Package::find($id);
        if (!$package) {
            return $this->sendError('Package Not Found');
        }

        $validator = Validator::make($request->all(), [
            'features_name' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $packageNames = implode(' - ', $request->input('features_name'));

        // dd($packageNames);

        $result = Package::where('package_name', $packageNames)
            ->first();

        if ($result == null) {
            $package->package_code = $packageNames;
            $package->package_name = $packageNames;

            if ($package->save()) {
                return $this->sendSuccess("Package Updated Successfully");
            } else {
                return $this->sendError('Failed to Update Package');
            }
        } else {
            return $this->sendError('This Package Already Created');
        }
    }

    public function destroy(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return $this->sendError('Package Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $package->status = 0;
        $package->deleted_by = $request->deleted_by;
        $package->save();
        if ($package->delete()) {
            return $this->sendSuccess('Package Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Package');
        }
    }
}
