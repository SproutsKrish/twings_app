<?php

namespace App\Http\Controllers;

use App\Models\UserDomain;
use App\Http\Requests\StoreUserDomainRequest;
use App\Http\Requests\UpdateUserDomainRequest;

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
