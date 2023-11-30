<?php

namespace App\Http\Livewire\Stock;

use App\Models\ProductoLote;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Productos;
use App\Models\StockEntrante;
use App\Models\Stock;
use App\Models\Almacen;
use PDF;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexComponent extends Component

{
    use LivewireAlert;
    public $productos;
    public $almacen_id;
    public $almacenes;
    public $producto_seleccionado;
    public $producto_lotes;

    public function mount()
    {
        $this->almacenes = Almacen::all();
        $this->almacen_id = auth()->user()->almacen_id;
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

    public function setLotes()
    {
        if($this->almacen_id == null){
            $this->producto_lotes = StockEntrante::where('producto_id', $this->producto_seleccionado)->get();
        }else{
            $entradas_almacen = Stock::where('almacen_id', $this->almacen_id)->get()->pluck('id');
            $this->producto_lotes = StockEntrante::where('producto_id', $this->producto_seleccionado)->whereIn('stock_id', $entradas_almacen)->get();
        }
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'setLotes'
        ];
    }
    public function formatFecha($id)
    {
        return Carbon::parse(Stock::find($id)->fecha)->format('d/m/Y');
    }
    public function alertaGuardar()
    {
        $this->alert('warning', '¿Estás seguro? Comprueba que se han usado todos los códigos QR, o que los códigos por generar sean necesarios.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock.crearQR');
    }
}
