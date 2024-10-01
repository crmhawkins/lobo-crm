<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControlPresupuestarioController extends Controller
{
    
    public function index()
    {
        return view('control-presupuestario.index');
    }
}
