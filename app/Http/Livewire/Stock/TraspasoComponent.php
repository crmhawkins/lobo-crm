<?php

namespace App\Http\Livewire\Stock;

use App\Models\Almacen;
use App\Models\Productos;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;
use App\Models\ProductosCategories;
use App\Models\StockEntrante;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class TraspasoComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $observaciones;
    public $qr_id;
    public $estado;
    public $producto_seleccionado;
    public $almacenes;
    public $almacen_id;
    public $productos;
    public $almacenDestino;
    public $cantidad;
    public $stockentrante;
    public $stock;
    public function mount()
    {
        $this->stockentrante = StockEntrante::find( $this->identificador);
        $this->stock = Stock::find(  $this->stockentrante->stock_id);
        $this->estado =  $this->stock->estado;
        $this->qr_id =  $this->stock->qr_id;
        $this->productos = Productos::all();
        $this->almacenes = Almacen::all();
        $user = Auth::user();
        $this->almacen_id =  $this->stock->almacen_id;

    }

    public function render()
    {
        return view('livewire.stock.traspaso-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
         $oldCantidad = $this->stockentrante->cantidad;
         if($this->cantidad <= $oldCantidad){
            $nuevaCantidad = $oldCantidad - $this->cantidad;
         }else{
            $this->alert('error', '¡No se puede mandar una cantidad mayor a la disponible!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
         }

         if ($nuevaCantidad == 0){
            $this->stock->update([
                'estado' => 2,
            ]);
         }

        $mercaderiaSave = Stock::create(
            [
                'estado' =>  $this->estado,
                'fecha' => Carbon::now(),
                'almacen_id' => $this->almacenDestino,
                'observaciones' => $this->observaciones,
            ]);
        $mercaderiaproductoSave = StockEntrante::create([
                'producto_id' => $this->stockentrante->producto_id,
                'lote_id' => $this->stockentrante->lote_id ,
                'stock_id' => $mercaderiaSave->id,
                'cantidad' => $this->cantidad,
                'orden_numero' => $this->stockentrante->orden_numero,
            ]);


        if($mercaderiaproductoSave){
            $productUpdate =$this->stockentrante->update([
                'cantidad' => $nuevaCantidad,
        ]);}


        if ($productUpdate) {
            $this->alert('success', '¡Stock actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del Stock!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

    }

    // Elimina el producto
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar el producto? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $product = Productos::find($this->identificador);
        $product->delete();
        return redirect()->route('stock.index');
    }
    public function getProductoNombre()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->nombre != null) {
            return $producto->nombre;
        }
    }

    public function getProductoImagen()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->foto_ruta != null) {
            return $producto->foto_ruta;
        }
    }

    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }



}
