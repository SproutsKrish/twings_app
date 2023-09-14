<?php

namespace App\Http\Controllers;

use App\Models\UserApiKey;
use App\Http\Requests\StoreUserApiKeyRequest;
use App\Http\Requests\UpdateUserApiKeyRequest;

class UserApiKeyController extends Controller
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
     * @param  \App\Http\Requests\StoreUserApiKeyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserApiKeyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserApiKey  $userApiKey
     * @return \Illuminate\Http\Response
     */
    public function show(UserApiKey $userApiKey)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserApiKey  $userApiKey
     * @return \Illuminate\Http\Response
     */
    public function edit(UserApiKey $userApiKey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserApiKeyRequest  $request
     * @param  \App\Models\UserApiKey  $userApiKey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserApiKeyRequest $request, UserApiKey $userApiKey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserApiKey  $userApiKey
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserApiKey $userApiKey)
    {
        //
    }
}
