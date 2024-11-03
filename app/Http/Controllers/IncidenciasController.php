<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncidenciasController extends Controller
{
    public function index()
    {
        return view('incidencias.index');
    }

    public function todas()
    {
        return view('incidencias.todas');
    }
}
