<?php

namespace App\Http\Livewire\Comercial;

use App\Models\ClientesComercial;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\SubCuentaHijo;
use App\Models\SubCuentaContable;
use App\Models\Delegacion;
use App\Models\User;

class clientes extends Component
{
    // public $search;
    public $clientes;

    public function mount()
    {
    

        $this->loadClientes();
    }




    // public function updateDelegacionFilter(){
    //     $this->loadClientes();
    // }

    public function loadClientes()
    {

        if(Auth::user()->role == 3){
            $this->clientes = ClientesComercial::where('comercial_id', Auth::user()->id)->get();
        }else{
            $this->clientes = ClientesComercial::all();

        }

    }



    public function render()
    {

        return view('livewire.comercial.clientes');
    }

}
