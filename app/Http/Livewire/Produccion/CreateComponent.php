<?php

namespace App\Http\Livewire\Produccion;

use App\Models\Almacen;
use App\Models\Mercaderia;
use App\Models\OrdenMercaderia;
use Livewire\Component;
use App\Models\Productos;
use App\Models\MaterialesProducto;
use App\Models\OrdenProduccion;
use App\Models\ProductosProduccion;
use App\Models\MercaderiaProduccion;
use Carbon\Carbon;
use App\Models\Stock;
use App\Models\StockMercaderia;
use App\Models\StockEntrante;
use App\Models\StockMercaderiaEntrante;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $numero;
    public $precio = 0;
    public $estado;
    public $fecha;
    public $observaciones;
    public $productos;
    public $producto_seleccionado;
    public $unidades_producto;
    public $productos_ordenados = [];
    public $mercaderias_gastadas = [];
    public $mercaderias;
    public $ordenes_mercaderias;
    public $almacen_id;
    public $almacenes;
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->estado = 0;
        $this->almacenes = Almacen::all();
        $this->mercaderias = Mercaderia::all();
        $this->productos = Productos::all();
        $this->ordenes_mercaderias = OrdenProduccion::all();
        $this->numero = Carbon::now()->format('y') . '/' . sprintf('%04d', $this->ordenes_mercaderias->whereBetween('fecha', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->count() + 1);
        $user = Auth::user();
        $this->almacen_id = $user->almacen_id;
    }

    public function render()
    {

        return view('livewire.produccion.create-component');
    }
    public function getProductoNombre()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->nombre != null) {
            return $producto->nombre;
        }
    }
    public function getPesoTotal($id,$in)
    {
        $pesoUnidad = $this->productos->where('id', $id)->first()->peso_neto_unidad;
        $cantidad = ($this->productos_ordenados[$in]['cantidad'])*($this->productos->where('id', $id)->first()->unidades_por_caja)*($this->productos->where('id', $id)->first()->cajas_por_pallet);
        $pesoTotal= ($pesoUnidad * $cantidad)/1000;
        return $pesoTotal;

    }
    public function getProductoImagen()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->foto_ruta != null) {
            return $producto->foto_ruta;
        }
    }

    public function getMercaderiaNombre()
    {
        $mercaderia = Mercaderia::find($this->mercaderia_seleccionada);
        if ($mercaderia != null && $mercaderia->nombre != null) {
            return $mercaderia->nombre;
        }
    }

    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }
    public function getNombreTabla2($id)
    {
        $nombre_producto = $this->mercaderias->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }

    public function getPrecioIndividual($id)
    {
        $nombre_producto = $this->mercaderias->where('id', $this->productos_ordenados[$id]['mercaderia_id'])->first()->precio;
        return $nombre_producto * $this->productos_ordenados[$id]['cantidad'];
    }

    public function deleteArticulo($id)
    {
        unset($this->productos_ordenados[$id]);
        $this->productos_ordenados = array_values($this->productos_ordenados);

        $this->setPrecioEstimado();
    }

    public function addProducto($id)
    {
        $producto_existe = false;
        $producto_id = $id;
        foreach ($this->productos_ordenados as $productos) {
            if ($productos['producto_id'] == $id) {
                $producto_existe = true;
                $producto_id = $productos['producto_id'];
            }
        }
        if ($producto_existe == true) {
            $producto = array_search($producto_id, array_column($this->productos_ordenados, 'producto_id'));
            $this->productos_ordenados[$producto]['cantidad'] = $this->productos_ordenados[$producto]['cantidad'] + $this->unidades_producto;
        } else {
            $this->productos_ordenados[] = ['producto_id' => $id, "cantidad" => $this->unidades_producto];
        }

        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }

    public function setPrecioEstimado()
{
    $this->mercaderias_gastadas = [];
    foreach ($this->productos_ordenados as $productoOrdenado) {
        $materiales = MaterialesProducto::where('producto_id', $productoOrdenado['producto_id'])->get();
        $producto = Productos::where('id', $productoOrdenado['producto_id'])->first();

        foreach ($materiales as $material) {
            $cantidadMaterialPorProducto = $material->cantidad * $productoOrdenado['cantidad'] * $producto->cajas_por_pallet;

            // Buscar si el material ya existe en mercaderias_gastadas
            $index = array_search($material->mercaderia_id, array_column($this->mercaderias_gastadas, 'mercaderia_id'));

            if ($index !== false) {
                // Actualizar la cantidad si el material ya existe
                $this->mercaderias_gastadas[$index]['cantidad'] += $cantidadMaterialPorProducto;
            } else {
                // Agregar el material si no existe
                $this->mercaderias_gastadas[] = [
                    'mercaderia_id' => $material->mercaderia_id,
                    'cantidad' => $cantidadMaterialPorProducto
                ];
            }
        }
    }
}

    public function sacarStock($productoId, $cantidad)
    {
        // Obtener las entradas de StockEntrante para el producto, ordenadas por ejemplo por fecha
        $entradas = StockMercaderiaEntrante::where('mercaderia_id', $productoId)->orderBy('created_at')->get();
        if ($entradas->sum('cantidad') > $cantidad) {
            foreach ($entradas as $entrada) {
                if ($cantidad <= 0) break;
                // Calcular la cantidad a sacar de esta entrada
                $cantidadASacar = min($entrada->cantidad, $cantidad);
                $entrada->cantidad -= $cantidadASacar;
                $entrada->save();
                $cantidad -= $cantidadASacar;

                // Si la entrada de StockEntrante se vacía, revisar el registro en Stock
                if ($entrada->cantidad == 0) {
                    $stock = StockMercaderia::where('id', $entrada->stock_id)->first();
                    // Comprobar si todas las entradas de este stock se han vaciado
                    if ($stock->entrantes->every(function ($ent) {
                        return $ent->cantidad == 0;
                    })) {
                        // Desactivar el stock si es necesario
                        $stock->estado = 2;
                        $stock->save();
                    } else {
                        $stock->estado = 1;
                        $stock->save();
                    }
                }
            }
        }
    }

    public function sumarStock()
    {
        $fecha = Carbon::now();
        // Obtener las entradas de StockEntrante para el producto, ordenadas por ejemplo por fecha
        $entrada = Stock::create([
            'qr_id' => 0, 'fecha' => $fecha, 'almacen_id' => $this->almacen_id, 'estado' => 0,
        ]);
        foreach ($this->productos_ordenados as $productosIndex => $productos) {
            $producto = Productos::find($productos['producto_id']);
            for ($i = 0; $i < $productos['cantidad']; $i++) {
                $lote_id = $fecha->format('ymd') . $producto->id . $i;
                StockEntrante::create([
                    'producto_id' => $productos['producto_id'],
                    'lote_id' => $lote_id,
                    'stock_id' => $entrada->id,
                    'cantidad' => $producto->cajas_por_pallet,
                ]);
            }
        }
    }
    public function ComprobacionStock ($mercaderiaId, $newProductionQuantity)
    {
        $currentStock = $this->getStock($mercaderiaId);
        $currentProductionUsage = $this->getStockGastado($mercaderiaId);
        $resultingQuantity = $currentStock - $currentProductionUsage - $newProductionQuantity;

        return $resultingQuantity >= 0;
    }
    public function getStockGastado($id)
    {
        return MercaderiaProduccion::where('mercaderia_id', $id)->get()->sum('cantidad');
    }

    public function getStock($id)
    {
        return StockMercaderiaEntrante::where('mercaderia_id', $id)->get()->sum('cantidad');
    }
    public function submit()
    {
        $productosSinStock = [];

        foreach ($this->mercaderias_gastadas as $mercaderia) {
            if (!$this->ComprobacionStock($mercaderia['mercaderia_id'], $mercaderia['cantidad'])) {
                $nombreProducto = $this->getNombreTabla2($mercaderia['mercaderia_id']);
                $productosSinStock[] = $nombreProducto;
            }
        }

        if (!empty($productosSinStock)) {
            $productosListados = implode('<br>', $productosSinStock); // Usa <br> para el salto de línea
            $this->alert('error', 'Stock insuficiente para los siguientes productos:<br>' . $productosListados, [
                'position' => 'center',
                'timer' => 0, // 0 significa que no desaparece automáticamente
                'toast' => false,
                'showConfirmButton' => false, // No muestra el botón de confirmación
                'onDismissed' => '', // No hace nada cuando se cierra
                'allowOutsideClick' => true // Permite cerrar haciendo clic fuera
            ]);
            return;
        }

        // Validación de datos
        $validatedData = $this->validate(
            [
                'numero' => 'required',
                'almacen_id' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'observaciones' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'almacen_id.required' => 'El numero de orden es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        // Guardar datos validados
        $mercaderiaSave = OrdenProduccion::create($validatedData);

        foreach ($this->mercaderias_gastadas as $mercaderiaIndex => $mercaderia) {
                $this->sacarStock($mercaderia['mercaderia_id'], $mercaderia['cantidad']);
        }
        /*if($this->estado == 1){
            $this->sumarStock();
        }*/
        foreach ($this->productos_ordenados as $mercaderiaIndex => $producto) {
            ProductosProduccion::create(['orden_id' => $mercaderiaSave->id, 'producto_id' => $producto['producto_id'], 'cantidad' => $producto['cantidad']]);
        }

        foreach ($this->mercaderias_gastadas as $mercaderiaIndex => $mercaderia) {
            MercaderiaProduccion::create(['orden_id' => $mercaderiaSave->id, 'mercaderia_id' => $mercaderia['mercaderia_id'], 'cantidad' => $mercaderia['cantidad']]);
        }

        // Alertas de guardado exitoso
        if ($mercaderiaSave) {
            $this->alert('success', '¡Órden de compra registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'alertaGuardar',
            'checkLote'
        ];
    }
    public function alertaGuardar()
    {
        $this->alert('warning', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submit',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('produccion.index');
    }
}
