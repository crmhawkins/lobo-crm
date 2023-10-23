<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Facturas;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;

class IndexComponent extends Component
{
    // public $search;
    public $pedidos_pendientes;
    public $pedidos_preparacion;
    public $pedidos_enviados;


    public function mount()
    {
        $this->pedidos_pendientes = Pedido::where('estado', 4)->get();
        $this->pedidos_preparacion = Pedido::where('estado', 5)->get();
        $this->pedidos_enviados = Pedido::where('estado', 6)->get();

    }

    public function render()
    {

        return view('livewire.almacen.index-component');
    }

    public function getNombreCliente($id){
        return Clients::where('id', $id)->first()->nombre;
    }

}
