<?php

namespace App\Http\Controllers;

use App\Models\UserDomain;
use App\Http\Requests\StoreUserDomainRequest;
use App\Http\Requests\UpdateUserDomainRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserDomainRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserDomainRequest $request)
    {
        //
    }

    public function login_image_save(Request $request)
    {
        //Validation Code
        $validator = Validator::make($request->all(), [
            'domain_name' => 'required|unique:user_domains,domain_name',
            'login_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            $response = ["success" => false, "message" => $validator->errors(), "status_code" => 403];
            return response()->json($response, 403);
        }

        $fileName = time() . '.' . $request->login_image->extension();
        $request->login_image->storeAs('public/images/login_logo/', $fileName);

        $user_domain = new UserDomain();
        $user_domain->domain_name = $request->input('domain_name');
        $user_domain->login_image = $fileName;
        $user_domain->created_by = auth()->user()->id;
        $result  = $user_domain->save();
        if ($result) {
        } else {
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserDomain  $userDomain
     * @return \Illuminate\Http\Response
     */
    public function show(UserDomain $userDomain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserDomain  $userDomain
     * @return \Illuminate\Http\Response
     */
    public function edit(UserDomain $userDomain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserDomainRequest  $request
     * @param  \App\Models\UserDomain  $userDomain
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserDomainRequest $request, UserDomain $userDomain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserDomain  $userDomain
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDomain $userDomain)
    {
        //
    }
}
