<?php

namespace App\Http\Livewire\MaterialesProducto;

use Livewire\Component;
use App\Models\Productos;
use App\Models\MaterialesProducto;
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
        return view('livewire.materiales-producto.index-component', [
            'productos' => $this->productos,
        ]);
    }

    public function checkProducto($id){
        $producto = MaterialesProducto::where('producto_id', $id)->exists();

        return $producto;
    }

}
