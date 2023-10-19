<?php

namespace App\Http\Livewire\Productos;

use Livewire\Component;
use App\Models\Productos;
use PDF;

class IndexComponent extends Component

{

    public $productos;

    public function mount()
    {
        $this->productos = Productos::all();
    }
    public function render()
    {
        return view('livewire.productos.index-component', [
            'productos' => $this->productos,
        ]);
    }

}
