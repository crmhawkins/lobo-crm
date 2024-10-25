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
        $this->clientes = ClientesComercial::all();
    }



    public function render()
    {

        return view('livewire.comercial.clientes');
    }

}
