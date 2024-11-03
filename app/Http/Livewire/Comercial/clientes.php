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
    public $filtroComercial;
    public $filtroDelegacion;

    public function mount()
    {
        $this->filtroComercial = '';
        $this->filtroDelegacion = '';
        $this->loadClientes();
    }




    public function getComercial($id)
    {
        $cliente = ClientesComercial::find($id);
        $comercial = User::find($cliente->comercial_id);
        return $comercial;
    }

    public function getDelegacion($id)
    {
        $cliente = ClientesComercial::find($id);
        $delegacion = Delegacion::find($cliente->delegacion_id);
        return $delegacion;
    }




    // public function updateDelegacionFilter(){
    //     $this->loadClientes();
    // }

    public function loadClientes()
    {
        $query = ClientesComercial::query();

        if (Auth::user()->role == 3) {
            $query->where('comercial_id', Auth::user()->id);
        } else {
            if ($this->filtroComercial) {
                $query->where('comercial_id', $this->filtroComercial);
            }
        }

        if ($this->filtroDelegacion) {
            $query->where('delegacion_id', $this->filtroDelegacion);
        }

        $this->clientes = $query->get();
    }



    public function render()
    {
        $comerciales = User::where('role', 3)->get();
        $delegaciones = Delegacion::all();

        return view('livewire.comercial.clientes', [
            'comerciales' => $comerciales,
            'delegaciones' => $delegaciones
        ]);
    }
}
