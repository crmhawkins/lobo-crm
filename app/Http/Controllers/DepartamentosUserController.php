<?php

namespace App\Http\Controllers;

use App\Models\DepartamentosUser;
use App\Http\Requests\StoreDepartamentosUserRequest;
use App\Http\Requests\UpdateDepartamentosUserRequest;

class DepartamentosUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('departamentos-user.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('departamentos-user.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDepartamentosUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepartamentosUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DepartamentosUser  $departamentosUser
     * @return \Illuminate\Http\Response
     */
    public function show(DepartamentosUser $departamentosUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DepartamentosUser  $departamentosUser
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('departamentos-user.edit', compact('id'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDepartamentosUserRequest  $request
     * @param  \App\Models\DepartamentosUser  $departamentosUser
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDepartamentosUserRequest $request, DepartamentosUser $departamentosUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DepartamentosUser  $departamentosUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(DepartamentosUser $departamentosUser)
    {
        //
    }
}
