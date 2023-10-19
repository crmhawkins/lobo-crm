<?php

namespace App\Http\Controllers;

use App\Models\ConceptosFactura;
use App\Http\Requests\StoreConceptosFacturaRequest;
use App\Http\Requests\UpdateConceptosFacturaRequest;

class ConceptosFacturaController extends Controller
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
     * @param  \App\Http\Requests\StoreConceptosFacturaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConceptosFacturaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConceptosFactura  $conceptosFactura
     * @return \Illuminate\Http\Response
     */
    public function show(ConceptosFactura $conceptosFactura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConceptosFactura  $conceptosFactura
     * @return \Illuminate\Http\Response
     */
    public function edit(ConceptosFactura $conceptosFactura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateConceptosFacturaRequest  $request
     * @param  \App\Models\ConceptosFactura  $conceptosFactura
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConceptosFacturaRequest $request, ConceptosFactura $conceptosFactura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConceptosFactura  $conceptosFactura
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConceptosFactura $conceptosFactura)
    {
        //
    }
}
