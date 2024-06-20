<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerEmailsController extends Controller
{

    public function index()
    {

        return view('veremails.index');
    }
}
