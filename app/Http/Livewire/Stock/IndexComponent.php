<?php

namespace App\Http\Livewire\Stock;

use App\Models\ProductoLote;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Productos;
use PDF;

class IndexComponent extends Component

{

    public $productos;
    public $producto_seleccionado;
    public $producto_lotes;

    public function mount()
    {
        $this->productos = Productos::all();
        $this->producto_seleccionado = 1;
        $this->setLotes();
    }
    public function render()
    {
        return view('livewire.stock.index-component', [
            'productos' => $this->productos,
        ]);
    }

    public function setLotes(){
        $this->producto_lotes = ProductoLote::where('producto_id', $this->producto_seleccionado)->get();
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'setLotes'
        ];
    }
    public function formatFecha($fecha){
        return Carbon::parse($fecha)->format('d/m/Y');
    }

}
