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
use App\Models\StockRegistro;

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
    public $almacenOnline = 6; // para pruebas es el 2 Córdoba
    public $qr_IdsOnline = [
        '80' => '24-p-00001071',
        '78' => '24-p-00001066',
        '77' => '24-p-00001077',
        '76' => '24-p-00001022',
        '71' => '24-p-00001101',
        '70' => '24-p-00001068',
        '69' => '24-p-00001070',
        '68' => '24-p-00001069',
        '67' => '24-p-00001076',
        '66' => '24-p-00001067',
        '65' => '24-p-00001023',
        '64' => '24-p-00001078',
        '55' => '24-p-00001058',
        '54' => '24-p-00001054',
        '53' => '24-p-00001050',
        '52' => '24-p-00001059',
        '51' => '24-p-00001055',
        '50' => '24-p-00001051',
        '47' => '24-p-00001057',
        '46' => '24-p-00001049',
        '45' => '24-p-00001053',
        '44' => '24-p-00001047',
        '43' => '24-p-00001046',
        '42' => '24-p-00001045',
        '19' => '24-p-00001148',
        '18' => '24-p-00001100',
        '17' => '24-p-00001160',
        '16' => '24-p-00001018',
        '15' => '24-p-00001019',
        '14' => '24-p-00000928',
        '13' => '24-p-00001025',
        '12' => '24-p-00000916',
        '11' => '24-p-00001162',
        '10' => '24-p-00000918',
        '9' => '24-p-00000931',
        '8' => '24-p-00001149',
        '7' => '24-p-00001150',
        '6' => '24-p-00001145',
        '5' => '24-p-00001151',
        '4' => '24-p-00001141',
        '3' => '24-p-00001137',
        '2' => '24-p-00001144',
        '1' => '24-p-00000889',
    ];

    public $cantidadStockRegistro;
    public $cantidadStockSaliente;

    public function mount()
    {
        $this->stockentrante = StockEntrante::find($this->identificador);
        $this->stock = Stock::find($this->stockentrante->stock_id);
        $this->estado =  $this->stock->estado;
        $this->qr_id =  $this->stock->qr_id;
        $this->productos = Productos::all();
        $this->almacenes = Almacen::all();
        $user = Auth::user();
        $this->almacen_id =  $this->stock->almacen_id;
        $this->almacenActual = $this->almacenes->find($this->stock->almacen_id);
        $this->cantidadStockRegistro = StockRegistro::where('stock_entrante_id', $this->stockentrante->id)->sum('cantidad');
        $this->cantidadStockSaliente = $this->stockentrante->cantidad - $this->cantidadStockRegistro;
    }

    public function render()
    {
        return view('livewire.stock.traspaso-component');
    }

    // Al hacer update en el formulario
    public function update()
    {

        if ($this->almacenDestino == null || $this->almacenDestino == 0) {
            $this->alert('error', '¡Selecciona almacén!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        if ($this->cantidad == null) {
            $this->alert('error', '¡Ingresa una cantidad!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        $oldCantidad = $this->stockentrante->cantidad - $this->cantidadStockRegistro;
        if ($this->cantidad <= $oldCantidad) {
            $nuevaCantidad = $oldCantidad - $this->cantidad;
        } else {
            $this->alert('error', '¡No se puede mandar una cantidad mayor a la disponible!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }


        if (0 > $this->cantidad) {
            $this->alert('error', '¡No se puede mandar una cantidad negativa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }
        if ($nuevaCantidad == 0) {
            $this->stock->update([
                'estado' => 2,
            ]);
        }


        if ($this->almacenDestino == $this->almacenOnline) {

            //dd($this->stockentrante);
            //producto
            $idProducto = $this->stockentrante->producto_id;

            //qr del producto en almacenOnline
            if (!array_key_exists($idProducto, $this->qr_IdsOnline)) {
                $this->alert('error', '¡No se ha encontrado el producto en el almacen online!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
                return;
            }
            $qrProducto = Stock::where('almacen_id', $this->almacenOnline)->where('qr_id', $this->qr_IdsOnline[$idProducto])->first();

            if ($qrProducto == null) {

                $this->alert('error', '¡No se ha encontrado el producto en el almacen online!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
                return;
            }


            //stockentrante del producto en almacenOnline
            $stockEntranteProducto = StockEntrante::where('stock_id', $qrProducto->id)->first();
            //dd($stockEntranteProducto);
            //dd($stockEntranteProducto);

            if ($stockEntranteProducto == null) {
                $this->alert('error', '¡No se ha encontrado el producto en el almacen online!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
                return;
            }


            $stockRegistro = StockRegistro::create([
                'stock_entrante_id' => $stockEntranteProducto->id,
                'cantidad' => -abs($this->cantidad),
                'tipo' => 'Traspaso',
                'motivo' => 'Entrada',
            ]);

            //stock saliente
            $stockSaliente = StockSaliente::create([
                'stock_entrante_id' => $this->stockentrante->id,
                'producto_id' => $this->stockentrante->producto_id,
                'cantidad_salida' => $this->cantidad,
                'fecha_salida' => Carbon::now(),
                'almacen_origen_id' => $this->almacenActual->id,
                'tipo' => 'Traspaso',
            ]);

            $stockRegistro = StockRegistro::create([
                'stock_entrante_id' => $this->stockentrante->id,
                'cantidad' => $this->cantidad,
                'tipo' => 'Traspaso',
                'motivo' => 'Salida',
            ]);


            if ($stockRegistro) {
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


            return;
        }
        //se crea el nuevo stock en almacen
        $mercaderiaSave = Stock::create(
            [
                'estado' =>  $this->estado,
                'fecha' => Carbon::now(),
                'almacen_id' => $this->almacenDestino,
                'observaciones' => $this->observaciones,
            ]
        );

        $mercaderiaproductoSave = StockEntrante::create([
            'producto_id' => $this->stockentrante->producto_id,
            'lote_id' => $this->stockentrante->lote_id,
            'stock_id' => $mercaderiaSave->id,
            'cantidad' => $this->cantidad,
            'orden_numero' => $this->stockentrante->orden_numero,
        ]);



        if ($mercaderiaproductoSave) {
            // $productUpdate = $this->stockentrante->update([
            //     'cantidad' => $nuevaCantidad,
            // ]);

            $productUpdate = StockRegistro::create([
                'stock_entrante_id' => $this->stockentrante->id,
                'cantidad' => abs($this->cantidad),
                'tipo' => 'Traspaso',
                'motivo' => 'Salida'
            ]);



            $stockSaliente = StockSaliente::create([
                'stock_entrante_id' => $mercaderiaproductoSave->id,
                'producto_id' => $mercaderiaproductoSave->producto_id,
                'cantidad_salida' => $this->cantidad,
                'fecha_salida' => Carbon::now(),
                'almacen_origen_id' => $this->almacenActual->id,
                'tipo' => 'Traspaso',
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
