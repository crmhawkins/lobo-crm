<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComercialViewController extends Controller
{
    //


    public function index()
    {
        return view('comercial.index');
    }

    public function clientecomercial()
    {
        return view('comercial.addcliente');
    }

    public function clientecomercialview()
    {
        return view('comercial.clientes');
    }

    public function editcliente($id)
    {
        return view('comercial.editcliente', ['id' => $id]);
    }

    public function createPedido()
    {
        return view('comercial.createpedido');
    }

    public function editPedido($id)
    {
        return view('comercial.editpedido', ['id' => $id]);
    }

    public function pedidos()
    {
        return view('comercial.pedidos');
    }
}
