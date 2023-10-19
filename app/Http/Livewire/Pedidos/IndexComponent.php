<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidosStatus;
use Livewire\Component;
class IndexComponent extends Component
{
    public $pedidos;
    public $clientes;

    public function mount()
    {
        $this->pedidos = Pedido::all();
        $this->clientes = Cliente::all();
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
