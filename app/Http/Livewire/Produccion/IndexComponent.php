<?php

namespace App\Http\Livewire\Produccion;

use App\Models\ProductoLote;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Productos;
use App\Models\OrdenProduccion;
use App\Models\Stock;
use App\Models\Almacen;
use PDF;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexComponent extends Component

{
    use LivewireAlert;
    public $ordenes_produccion;
    public $almacen_id;
    public $almacenes;

    public function mount()
    {
        $this->almacenes = Almacen::all();
        $this->almacen_id = auth()->user()->almacen_id;
        $this->ordenes_produccion = OrdenProduccion::all();
    }
    public function render()
    {
        return view('livewire.produccion.index-component');
    }

    public function formatFecha($id)
    {
        return Carbon::parse(Stock::find($id)->fecha)->format('d/m/Y');
    }
    public function getAlmacen($id)
    {
        return $this->almacenes->where('id', $id)->first()->almacen;
    }
    public function getEstado($id)
    {
        if($id == 0){
            return "Pendiente";
        }else{
            return "Completado";
        }
    }

}
