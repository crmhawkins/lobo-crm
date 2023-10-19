<?php

namespace App\Http\Controllers;

use App\Models\Payment_method;
use App\Http\Requests\StorePayment_methodRequest;
use App\Http\Requests\UpdatePayment_methodRequest;

class PaymentMethodController extends Controller
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
     * @param  \App\Http\Requests\StorePayment_methodRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayment_methodRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function show(Payment_method $payment_method)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment_method $payment_method)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayment_methodRequest  $request
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayment_methodRequest $request, Payment_method $payment_method)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment_method  $payment_method
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment_method $payment_method)
    {
        //
    }
}
