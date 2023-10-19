<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Pedido;
use App\Models\Facturas;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;

class IndexComponent extends Component
{
    // public $search;
    public $pedidos;
    public $facturas;


    public function mount()
    {
        $this->pedidos = Pedido::all();
        $this->facturas = Facturas::all();
    }

    public function render()
    {

        return view('livewire.facturas.index-component');
    }


}
