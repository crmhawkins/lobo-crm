<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturas;
use App\Models\Clients;

class Test extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';

        $facturas = Facturas::all();
        $clientes = Clients::all();

        //modificar la fecha de vencimiento de las facturas para que coincidan con la fecha de vencimiento por defecto de los clientes, hay que 
        //coger la fecha_emision de la factura y coger el vencimiento_factura_pref que son los dias a sumar a la fecha de emision, de ahí sacar la fecha de vencimiento

        //por cada factura debemos bucar al cliente asociado por su cliente_id y coger el vencimiento_factura_pref del cliente

        foreach ($facturas as $factura) {
            $cliente = Clients::find($factura->cliente_id);
            $factura->fecha_vencimiento = date('Y-m-d', strtotime($factura->fecha_emision . ' + ' . $cliente->vencimiento_factura_pref . ' days'));
            //dd($factura->fecha_vencimiento , $factura->fecha_emision, $cliente->vencimiento_factura_pref);
            $factura->save();
        }



        //dd($facturas);


        return 'ok';
    }
}
