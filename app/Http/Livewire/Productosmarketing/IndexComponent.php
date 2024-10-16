<?php

namespace App\Http\Livewire\Productosmarketing;

use Livewire\Component;
use App\Models\ProductosMarketing;
use PDF;

class IndexComponent extends Component

{

    public $productos;

    public function mount()
    {
        $this->productos = ProductosMarketing::all();
    }
    public function render()
    {
        return view('livewire.productosmarketing.index-component', [
            'productos' => $this->productos,
        ]);
    }

    

}
