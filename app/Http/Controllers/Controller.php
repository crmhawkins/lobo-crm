<?php

namespace App\Http\Controllers;

use App\Models\Alertas;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $alertas;
    protected $user;

    public function __construct() 
    {
        $this->user = Auth::user();
        // Fetch the Site Settings object
        $this->alertas = Alertas::all();
        View::share('alertas', $this->alertas);
        View::share('user', $this->user);
    }

}
