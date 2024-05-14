<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    //edit
    public function edit()
    {
        $configuracion = Configuracion::first();
        //si no hay que configuracion, se crea una
        if (!$configuracion) {
            //save con cuenta 0
            $configuracion = new Configuracion();
            $configuracion->cuenta = 0;
            $configuracion->save();

        }
        return view('configuracion.edit', compact('configuracion'));
    }
}
