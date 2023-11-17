<?php

namespace App\Http\Controllers;

use App\Models\AppInfo;
use App\Models\OnlineUser;
use Illuminate\Http\Request;

class OnlineController extends Controller
{
    public function store(Request $request)
    {
        $data['name'] = $request->input('name');
        $data['email'] = $request->input('email');
        $data['mobile_no'] = $request->input('mobile_no');
        $data['password'] = $request->input('password');
        $data['country_id'] = $request->input('country_id');
        $data['address'] = $request->input('address');
        $data['app_id'] = $request->input('app_id');
        $data['app_name'] = $request->input('app_name');

        $app_info = AppInfo::find($request->input('app_id'));

        $data['admin_id'] = $app_info->admin_id;
        $data['distributor_id'] = $app_info->distributor_id;
        $data['dealer_id'] = $app_info->dealer_id;
        $data['subdealer_id'] = $app_info->subdealer_id;

        $result = OnlineUser::create($data);
        dd($result);
    }
}
