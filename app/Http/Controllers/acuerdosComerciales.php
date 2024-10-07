<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\acuerdosComerciales as acuerdos;
use Illuminate\Support\Facades\Auth;
use App\Models\Clients;


class acuerdosComerciales extends Controller
{
    //index
    public function create($id)
    {
        

        //el numero viene como xx/año por lo que debo coger el xx y sumarle 1 y luego poner el año actual

        return view('acuerdoscomerciales.create' , compact('id'));
    }

    public function edit($id)
    {
        
        return view('acuerdoscomerciales.edit', compact('id'));
    }

    public function store(Request $request)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'user_id' => 'required',
            'cliente_id' => 'required',
            'nAcuerdo' => 'required|string|max:255',
            'nombre_empresa' => 'required|string|max:255',
            'cif_empresa' => 'required|string|max:255',
            'nombre' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'telefono' => 'nullable|string|max:255',
            'domicilio' => 'nullable|string|max:255',
            'establecimiento' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'productos_lobo' => 'nullable|string',
            'productos_otros' => 'nullable|string',
            'marketing' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firma_comercial_lobo' => 'nullable|string|max:255',
            'firma_comercial' => 'nullable|string|max:255',
            'firma_cliente' => 'nullable|string|max:255',
            'firma_distribuidor' => 'nullable|string|max:255',
        ]);

        // Crear el acuerdo comercial
        acuerdos::create($validatedData);

        // Redirigir con un mensaje de éxito
        return redirect()->route('acuerdos-comerciales.create')->with('success', 'Acuerdo comercial guardado exitosamente');
    }
}
