<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Clients;
use App\Models\Pedido;
use App\Models\PedidosStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
class IndexComponent extends Component
{
    public $pedidos;
    public $clientes;

    public function mount()
    {
        if(Auth::user()->role != 3){
            $this->pedidos = Pedido::all();
        }else{
            $this->pedidos = Clients::with('pedidos')->where('comercial_id', Auth::user()->id)
                        ->get()
                        ->pluck('pedidos')
                        ->flatten();
        }
        $this->clientes = Clients::all();
    }

    public function getClienteNombre($id){
        $cliente = $this->clientes->find($id);

        $nombre = $cliente->nombre;
        $apellido = $cliente->apellido;

        return "$nombre $apellido";
    }

    public function render()
    {
        return view('livewire.pedidos.index-component');
    }

    public function getEstadoNombre($estado){
        return PedidosStatus::firstWhere('id', $estado)->status;
    }
}
