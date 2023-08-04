<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Country;


class CountryController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:country-list|country-create|country-edit|country-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:country-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:country-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:country-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $countries = Country::all();

        if ($countries->isEmpty()) {
            return $this->sendError('No Countries Found');
        }

        return $this->sendSuccess($countries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_name' => 'required|max:255',
            'short_name' => 'required|max:255',
            'phone_code' => 'required|max:255',
            'timezone_name' => 'required|max:255',
            'timezone_offset' => 'required|max:255',
            'timezone_minutes' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $country = new Country($request->all());
        if ($country->save()) {
            return $this->sendSuccess("Country Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Country');
        }
    }

    public function show($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->sendError('Country Not Found');
        }

        return $this->sendSuccess($country);
    }

    public function update(Request $request, $id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->sendError('Country Not Found');
        }

        $validator = Validator::make($request->all(), [
            'country_name' => 'required|max:255',
            'short_name' => 'required|max:255',
            'phone_code' => 'required|max:255',
            'timezone_name' => 'required|max:255',
            'timezone_offset' => 'required|max:255',
            'timezone_minutes' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($country->update($request->all())) {
            return $this->sendSuccess("Country Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Country');
        }
    }

    public function destroy(Request $request, $id)
    {
        $country = Country::find($id);

        if (!$country) {
            return $this->sendError('Country Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $country->status = 0;
        $country->deleted_by = $request->deleted_by;
        $country->save();
        if ($country->delete()) {
            return $this->sendSuccess('Country Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Country');
        }
    }
}
