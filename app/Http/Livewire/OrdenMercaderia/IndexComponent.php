<?php

namespace App\Http\Livewire\OrdenMercaderia;

use App\Models\Presupuesto;
use App\Models\OrdenMercaderia;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $ordenes_mercaderias;


    public function mount()
    {
        $this->ordenes_mercaderias = OrdenMercaderia::all();
    }

    public function render()
    {
        return view('livewire.orden-mercaderia.index-component');
    }

}
