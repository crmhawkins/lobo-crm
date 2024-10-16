<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockSubalmacenController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';
        return view('stock-subalmacen.index', compact('response'));
    }


    public function registro()
    {
        return view('stock-subalmacen.registro');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('stock-subalmacen.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return 'CREADO';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function historial()
    {


        return view('stock-subalmacen.historial' );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('stock-subalmacen.edit', compact('id'));
    }
    public function traspaso($id)
    {
        return view('stock-subalmacen.traspaso', compact('id'));
    }
  

}
