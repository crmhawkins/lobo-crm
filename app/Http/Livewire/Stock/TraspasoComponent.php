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
use App\Models\StockSaliente;

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
    public $almacenActual;

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
        $this->almacenActual = $this->almacenes->find($this->stock->almacen_id);


    }

    public function render()
    {
        return view('livewire.stock.traspaso-component');
    }

    // Al hacer update en el formulario
    public function update()
    {

        if($this->almacenDestino == null){
            $this->alert('error', '¡Selecciona almacén!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        if($this->cantidad == null){
            $this->alert('error', '¡Ingresa una cantidad!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

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


         if( 0 > $this->cantidad ){
            $this->alert('error', '¡No se puede mandar una cantidad negativa!', [
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
         //se crea el nuevo stock en almacen
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
            ]);

            
    
            $stockSaliente = StockSaliente::create([
                'stock_entrante_id' => $mercaderiaproductoSave->id,
                'producto_id' => $mercaderiaproductoSave->producto_id,
                'cantidad_salida' => $this->cantidad,
                'fecha_salida' => Carbon::now(),
                'almacen_origen_id' => $this->almacenActual->id,
            ]);
        
        }


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
