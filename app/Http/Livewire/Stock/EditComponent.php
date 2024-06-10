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
use App\Models\RoturaStock;
use App\Models\ModificacionesStock;
use App\Models\User;
use App\Models\StockRegistro;

class EditComponent extends Component
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
    public $fecha;
    public $cantidad;
    public $stockentrante;
    public $stock;
    public $almacenActual;
    public $motivo;
    public $roturas = [];
    public $modificaciones = [];
    public $registroStock;

    public $roturaStockItem;
    public $addStockItem;
    public $deleteStockItem;

    public function mount()
    {
        $this->stockentrante = StockEntrante::find( $this->identificador);
        $this->stock = Stock::find(  $this->stockentrante->stock_id);
        $this->estado = $this->stock->estado;
        $this->registroStock = StockRegistro::where('stock_entrante_id', $this->stockentrante->id)->get();

        $this->cantidad = $this->stockentrante->cantidad - $this->registroStock->sum('cantidad');
        //dd($this->registroStock->sum('cantidad'));
        $this->qr_id = $this->stock->qr_id;
        $this->fecha = $this->stock->fecha;
        $this->observaciones = $this->stock->observaciones;
        $this->productos = Productos::all();
        $this->almacenes = Almacen::all();
        $user = Auth::user();
        $this->almacen_id = $this->stock->almacen_id;
        $this->almacenActual = $this->almacenes->find($this->stock->almacen_id);


    }

    public function render()
    {

        $this->roturas = RoturaStock::where('stock_id', $this->stock->id)->get();
        $this->modificaciones = ModificacionesStock::where('stock_id', $this->stock->id)->get();

        return view('livewire.stock.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        if( 0 > $this->cantidad ){
            $this->alert('error', '¡No se puede asignar una cantidad negativa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
         }

         if ($this->cantidad == 0){
            $this->stock->update([
                'estado' => 2,
            ]);
         }


        $this->stock->update([
            'estado' =>  $this->estado,
            'observaciones' => $this->observaciones,
        ]);

        if($this->roturaStockItem > 0){
            $roturaStock = new RoturaStock();
            $roturaStock->stock_id = $this->stock->id;
            $roturaStock->cantidad = $this->roturaStockItem;
            $roturaStock->fecha = Carbon::now();
            $roturaStock->observaciones = $this->motivo;
            //$roturaStock->observaciones = 'Rotura de stock';
            $roturaStock->almacen_id = $this->almacen_id;
            $roturaStock->user_id = Auth::user()->id;
            $roturaStock->save();

            $registro = new StockRegistro();
            $registro->stock_entrante_id = $this->stockentrante->id;
            $registro->cantidad = $this->roturaStockItem;
            $registro->tipo = 'Rotura';
            $registro->motivo = 'Salida';
            $registro->save();

            $this->roturaStockItem = 0;
            $this->motivo = null;
        }elseif($this->addStockItem > 0){
            $anadirStock = new ModificacionesStock();
            $anadirStock->stock_id = $this->stock->id;
            $anadirStock->cantidad = $this->addStockItem;
            $anadirStock->fecha = Carbon::now();
            $anadirStock->tipo = 'Suma';
            $anadirStock->motivo = $this->motivo;
            $anadirStock->almacen_id = $this->almacen_id;
            $anadirStock->user_id = Auth::user()->id;
            $anadirStock->save();

            $registro = new StockRegistro();
            $registro->stock_entrante_id = $this->stockentrante->id;
            $registro->cantidad = -$this->addStockItem;
            $registro->tipo = 'Modificacion';
            $registro->motivo = 'Entrada';
            $registro->save();

            $this->addStockItem = 0;
            $this->motivo = null;
        }elseif($this->deleteStockItem > 0){
            $restarStock = new ModificacionesStock();
            $restarStock->stock_id = $this->stock->id;
            $restarStock->cantidad = $this->deleteStockItem;
            $restarStock->fecha = Carbon::now();
            $restarStock->tipo = 'Resta';
            $restarStock->motivo = $this->motivo;
            $restarStock->almacen_id = $this->almacen_id;
            $restarStock->user_id = Auth::user()->id;
            $restarStock->save();

            $registro = new StockRegistro();
            $registro->stock_entrante_id = $this->stockentrante->id;
            $registro->cantidad = $this->deleteStockItem;
            $registro->tipo = 'Modificacion';
            $registro->motivo = 'Salida';
            $registro->save();

            $this->deleteStockItem = 0;
            $this->motivo = null;

        }


            $this->alert('success', '¡Stock actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
       

    }

    public function  addStock(){

        if($this->motivo == null){
            $this->alert('error', '¡Debe indicar un motivo para sumar stock!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);

            $this->addStockItem = 0;
            return;
        }

        $this->addStockItem = abs($this->addStockItem);
        //si la cantidad a sumar mas la cantidad es mayor a la cantidad base de stockEntrante, se muestra un error
        if($this->addStockItem + $this->cantidad > $this->stockentrante->cantidad){
            $this->alert('error', '¡No se puede asignar una cantidad mayor a la cantidad base!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            $this->addStockItem = 0;
            return;
        }

        $this->cantidad = $this->cantidad + $this->addStockItem;
        $this->alert('warning', '¿Seguro que desea registrar la suma de stock?', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ])
        ;

    }

    public function  deleteStock(){
        if($this->motivo == null){
            $this->alert('error', '¡Debe indicar un motivo para restar stock!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            $this->deleteStockItem = 0;
            return;
        }
        $this->deleteStockItem = abs($this->deleteStockItem);

            
        $this->cantidad = $this->cantidad - $this->deleteStockItem;
        //si es un numero negativo se convierte en positivo, es decir, se le quita el signo negativo

        $this->alert('warning', '¿Seguro que desea registrar la resta de stock?', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ])
        ;
        
    }

    public function  roturaStock(){
        if($this->motivo == null){
            $this->alert('error', '¡Debe indicar un motivo para rotura stock!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            $this->roturaStockItem = 0;
            return;
        }

        $this->roturaStockItem = abs($this->roturaStockItem);

        $this->cantidad = $this->cantidad - $this->roturaStockItem;

        $this->alert('warning', '¿Seguro que desea registrar la rotura de stock?', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ])
        ;
       
        

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

    public function getNombreUsuario($id)
    {
        //dd("prueba");
        $nombre_usuario = User::where('id', $id)->first();

        if($nombre_usuario != null){
            return $nombre_usuario->name . " " . $nombre_usuario->surname;
        }

        return "Usuario no encontrado";
    }

    public function getNombreAlmacen($id)
    {
        //dd("prueba");
        $nombre_almacen = Almacen::where('id', $id)->first()->almacen;
        //dd($nombre_almacen);
        return $nombre_almacen ?? "Almacén no encontrado";
    }



}
