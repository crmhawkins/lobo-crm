<?php

namespace App\Http\Livewire\Produccion;

use App\Models\ProductoLote;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Productos;
use App\Models\OrdenProduccion;
use App\Models\Stock;
use App\Models\Almacen;
use App\Models\StockEntrante;
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

    public function getPedidoById($id){
        $pedido = Pedido::where('id', $id)->first();
        if($pedido == null){
            $pedido->nombre = "No asignado";
        }
        return $pedido;
    }

    public function formatFecha($id)
    {
        return Carbon::parse(Stock::find($id)->fecha)->format('d/m/Y');
    }
    public function getAlmacen($id)
    {
        if($id > 0) {
            return $this->almacenes->where('id', $id)->first()->almacen;
        }else{
            return "No especificado";
        }
    }

  /*  public function getPesoTotal($id,$in)
    {
        $pesoUnidad = $this->productos->where('id', $id)->first()->peso_neto_unidad;
        $cantidad = ($this->productos_ordenados[$in]['cantidad'])*($this->productos->where('id', $id)->first()->unidades_por_caja)*($this->productos->where('id', $id)->first()->cajas_por_pallet);
        $pesoTotal= ($pesoUnidad * $cantidad)/1000;
        return $pesoTotal;

    }*/
    public function getEstado($id)
    {
        if($id == 0){
            return "Pendiente";
        }else{
            return "Completado";
        }
    }
    /*public function completarProduccion($identificador)
    {
        $Orden = OrdenProduccion::find($identificador);
        $this->sumarStock($Orden);
        $OrdenSave = $Orden->update(['estado' => 1]);
        if ($OrdenSave) {
            $this->alert('success', '¡Pedido en preparación!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);

            $this->ordenes_produccion = OrdenProduccion::all();

        } else {
            $this->alert('error', '¡No se ha podido poner en preparación el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    private function sumarStock($orden)
    {
        $fecha = Carbon::now();

        foreach ($orden->productos as $producto) {
            $entrada = Stock::create([
                'qr_id' => 0,
                'fecha' => $fecha,
                'almacen_id' => $orden->almacen_id,
                'estado' => 0,
            ]);

            for ($i = 0; $i < $producto->cantidad; $i++) {
                $lote_id = $fecha->format('ymd') . $producto->id . $i;
                StockEntrante::create([
                    'producto_id' => $producto->id,
                    'lote_id' => $lote_id,
                    'stock_id' => $entrada->id,
                    'cantidad' => $producto->cajas_por_pallet,
                ]);
            }
        }
    }*/

}
