<?php

namespace App\Http\Controllers;

use App\Models\clients_email;
use App\Http\Requests\Storeclients_emailRequest;
use App\Http\Requests\Updateclients_emailRequest;

class ClientsEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('client.index');
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
     * @param  \App\Http\Requests\Storeclients_emailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storeclients_emailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\clients_email  $clients_email
     * @return \Illuminate\Http\Response
     */
    public function show(clients_email $clients_email)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\clients_email  $clients_email
     * @return \Illuminate\Http\Response
     */
    public function edit(clients_email $clients_email)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updateclients_emailRequest  $request
     * @param  \App\Models\clients_email  $clients_email
     * @return \Illuminate\Http\Response
     */
    public function update(Updateclients_emailRequest $request, clients_email $clients_email)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\clients_email  $clients_email
     * @return \Illuminate\Http\Response
     */
    public function destroy(clients_email $clients_email)
    {
        //
    }
}
