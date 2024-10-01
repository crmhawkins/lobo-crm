<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\SubCuentaHijo;
use App\Models\SubCuentaContable;

class IndexComponent extends Component
{
    // public $search;
    public $clientes;

    public function mount()
    {
        if(Auth::user()->role != 3){
            $this->clientes = Clients::all();
        }else{
            $this->clientes = Clients::where('comercial_id', Auth::user()->id)->get();

            if(Auth::user()->user_department_id == 2){
                $this->clientes = Clients::where('comercial_id', Auth::user()->id)->orWhere('delegacion_COD', 0)->orWhere('delegacion_COD', 16)->where('estado', 2) ->get();
            }

        }
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
