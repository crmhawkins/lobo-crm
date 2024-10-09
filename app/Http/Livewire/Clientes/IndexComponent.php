<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\SubCuentaHijo;
use App\Models\SubCuentaContable;
use App\Models\Delegacion;
use App\Models\User;

class IndexComponent extends Component
{
    // public $search;
    public $clientes;
    public $delegacionFilter = null; // Nuevo campo para el filtro de delegaciones
    public $delegaciones = [];

    public function mount()
    {
        // if(Auth::user()->role != 3){
        //     $this->clientes = Clients::all();
        // }else{
        //     $this->clientes = Clients::where('comercial_id', Auth::user()->id)->get();

        //     if(Auth::user()->user_department_id == 2){
        //         $this->clientes = Clients::where('comercial_id', Auth::user()->id)->orWhere('delegacion_COD', 0)->orWhere('delegacion_COD', 16)->where('estado', 2) ->get();
        //     }

        // }

        $this->delegaciones = Delegacion::all(); // Obtener todas las delegaciones


        $this->loadClientes();
    }


    public function getComercial($clienteId){
        $cliente = Clients::find($clienteId);
        if($cliente){
            $comercialId = $cliente->comercial_id;
            $comercial = User::find($comercialId);
            if($comercial){
                return $comercial->name . ' ' . $comercial->surname;
            }else{
                return "No definido";
            }
           
        }
    }


    public function updateDelegacionFilter(){
        $this->loadClientes();
    }

    public function loadClientes()
    {
        // Consulta condicional para obtener clientes según el filtro de delegación
        if (Auth::user()->role != 3) {
            $this->clientes = Clients::when($this->delegacionFilter, function ($query) {
                return $query->where('delegacion_COD', $this->delegacionFilter);
            })->get();
        } else {
            $this->clientes = Clients::where('comercial_id', Auth::user()->id)
                ->when($this->delegacionFilter, function ($query) {
                    return $query->where('delegacion_COD', $this->delegacionFilter);
                })
                ->get();
        }
    }


    public function getDelegacion($clienteId){  
        $cliente = Clients::find($clienteId);
        if($cliente){
            $delegacion = $cliente->delegacion_COD;
            if($delegacion){
                $delegacion = Delegacion::where('COD', $delegacion)->first();
                if($delegacion){
                    return $delegacion->nombre;
                }
            }
        }
        return "No definido";
    }

    public function crearCuentasContables(){
        $clientes = Clients::where('cuenta_contable', '!=', null)->orderBy('cuenta_contable', 'asc')->get();

        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', 7000)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Cliente',
                ]);
            }

           
        }
        
        //dd($clientes);

    }

    public function render()
    {

        return view('livewire.clientes.index-component');
    }

}
