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
use App\Models\Pedido;
class EditComponent extends Component
{
    use LivewireAlert;
    public $identificador;
    public $numero;
    public $precio = 0;
    public $estado;
    public $estado_old;
    public $fecha;
    public $observaciones;
    public $productos;
    public $producto_seleccionado;
    public $unidades_producto;
    public $unidades_pallet_producto;
    public $unidades_caja_producto;
    public $productos_ordenados = [];
    public $mercaderias_gastadas = [];
    public $mercaderias;
    public $ordenes_mercaderias;
    public $almacen_id;
    public $almacenes;
    public $pedidos = [];
    public $pedido_id;

    public $productoEditar;
    public $productoEditarId;
    public $productoEditarNombre;
    public $productoEditarPrecio;
    public $productoEditarUds;
    public $productoEditarPallets;
    public $productoEditarCajas;

    public $arrProductosEditar = [];
    public $indexPedidoProductoEditar;

    public $mercaderia_gastadas_old = [];
    public $productos_ordenados_old = [];

    protected $listeners = ['refreshComponent' => '$refresh'];
    public function editProductos($id){
        
        

        $this->productos_ordenados[$id]['cantidad'] = $this->unidades_producto;
        //dd($this->productos_ordenados[$id]['cantidad'] , $this->productoEditarUds);
        //dd($this->productos_ordenados[$id]['cantidad']);
        $this->productoEditar = null;
        $this->productoEditarId = null;
        $this->productoEditarNombre = null;
        $this->productoEditarPrecio = null;
        $this->productoEditarUds = null;
        $this->productoEditarPallets = null;
        $this->productoEditarCajas = null;
        $this->indexPedidoProductoEditar = null;
        //dd($this->productos_ordenados);

        $this->setMercaderia();

        $this->emit('refreshComponent');

    }

    public function selectProduct($id, $unidades, $idIndex){
        //dd("prueba");
        //selecciona el producto para editarlo
        $this->productoEditar = Productos::find($id);
        //dd($unidades);
        //dd($this->productoEditar , $id, $unidades, $idIndex);
        $this->productoEditarId = $id;
        $this->productoEditarNombre = $this->productoEditar->nombre;
        $this->productoEditarUds = $unidades;
        //$this->productoEditarPallets = floor($unidades / $this->productoEditar->unidades_por_caja / $this->productoEditar->cajas_por_pallet);
        //$this->productoEditarCajas = floor($unidades / $this->productoEditar->unidades_por_caja);

        $this->producto_seleccionado = $this->productoEditarId;
        $this->unidades_producto = $this->productoEditarUds;
        $this->unidades_pallet_producto = $this->productoEditarPallets;
        $this->unidades_caja_producto = $this->productoEditarCajas;

        $this->indexPedidoProductoEditar = $idIndex;
  
    }

    public function mount()
    {
        $orden = OrdenProduccion::find($this->identificador);
        $this->fecha = $orden->fecha;
        $this->estado_old = $orden->estado;
        $this->estado = $orden->estado;
        $this->almacenes = Almacen::all();
        $this->mercaderias = Mercaderia::all();
        $this->productos = Productos::all();
        $this->ordenes_mercaderias = OrdenProduccion::all();
        $productos_orden = ProductosProduccion::where('orden_id', $orden->id)->get();
        $mercaderias_orden = MercaderiaProduccion::where('orden_id', $orden->id)->get();
        foreach ($productos_orden as $producto_orden) {
            $this->productos_ordenados[] = [
                'id' => $producto_orden->id,
                'producto_id' => $producto_orden->producto_id,
                'cantidad' => $producto_orden->cantidad,
                'borrar' => 0,
            ];
        }
       
        foreach ($mercaderias_orden as $mercaderia_orden) {
            $this->mercaderias_gastadas[] = [
                'id' => $mercaderia_orden->id,
                'mercaderia_id' => $mercaderia_orden->mercaderia_id,
                'cantidad' => $mercaderia_orden->cantidad,
                'borrar' => 0,
            ];
        }
        $this->numero = $orden->numero;
        $this->almacen_id = $orden->almacen_id;
        $this->pedidos = Pedido::all();
        $this->pedido_id = $orden->pedido_id;
        $this->observaciones = $orden->observaciones;
        //dd($orden);
        //dd($this->pedido_id);
    }

    public function render()
    {
        return view('livewire.produccion.edit-component');
    }

    public function getUnidades($id){
        $producto = Productos::find($this->productos_ordenados[$id]['producto_id']);
        if($producto == null){
            return '';
        }
        $unidades = $this->productos_ordenados[$id]['cantidad'];
        return $unidades;

    }
    public function getUnidadesTabla($id)
    {
        $producto = Productos::find($this->productos_ordenados[$id]['producto_id']);
        if($producto == null){
            return '';
        }
        $cajas = ($this->productos_ordenados[$id]['cantidad'] / $producto->unidades_por_caja);
        $pallets = floor($cajas / $producto->cajas_por_pallet);
        $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
        $unidades = '';
        if ($cajas_sobrantes > 0) {
            $unidades = $this->productos_ordenados[$id]['cantidad'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
        } else {
            $unidades = $this->productos_ordenados[$id]['cantidad'] . ' unidades (' . $pallets . ' pallets)';
        }
        return $unidades;
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
        $pesoUnidad = $this->productos->where('id', $id)->first();
        if($pesoUnidad == null){
            return '';
        }else{
            $pesoUnidad = $pesoUnidad->peso_neto_unidad;
        }
        $cantidad = $this->productos_ordenados[$in]['cantidad'];
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
        $nombre_producto = $this->productos->where('id', $id)->first();
        return $nombre_producto ? $nombre_producto->nombre : '';
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
        $this->unidades_caja_producto = 0;
        $this->unidades_pallet_producto = 0;
        $this->setPrecioEstimado();



        $this->emit('refreshComponent');
    }

    public function setPrecioEstimado()
    {

        foreach ($this->productos_ordenados as $productos) {
            $materiales = MaterialesProducto::where('producto_id', $productos['producto_id'])->get();
            $producto = Productos::where('id', $productos['producto_id'])->first();
            $unidades = $producto->cajas_por_pallet * $producto->unidades_por_caja;
            foreach ($materiales as $material) {
                $mercaderia_existe = false;
                $mercaderia_id = $material->mercaderia_id;
                foreach ($this->mercaderias_gastadas as $mercaderias) {
                    if ($mercaderias['mercaderia_id'] == $material->mercaderia_id) {
                        $mercaderia_existe = true;
                        $mercaderia_id = $mercaderias['mercaderia_id'];
                    }
                }
                if ($mercaderia_existe == true) {
                    $mercaderia = array_search($mercaderia_id, array_column($this->mercaderias_gastadas, 'mercaderia_id'));
                    $this->mercaderias_gastadas[$mercaderia]['cantidad'] = ($material->cantidad/$producto->unidades_por_caja) * $productos['cantidad'] ;
                } else {
                    $this->mercaderias_gastadas[] = ['mercaderia_id' => $mercaderia_id, "cantidad" => ($material->cantidad/$producto->unidades_por_caja) * $productos['cantidad']];
                }
            }
        }
    }

    public function setMercaderia()
    {
        $this->mercaderia_gastadas_old = $this->mercaderias_gastadas;
        foreach ($this->productos_ordenados as $productos) {
            $materiales = MaterialesProducto::where('producto_id', $productos['producto_id'])->get();
            $producto = Productos::where('id', $productos['producto_id'])->first();
            $unidades = $producto->cajas_por_pallet * $producto->unidades_por_caja;
            foreach ($materiales as $material) {
                $mercaderia_existe = false;
                $mercaderia_id = $material->mercaderia_id;
                foreach ($this->mercaderias_gastadas as $mercaderias) {
                    if ($mercaderias['mercaderia_id'] == $material->mercaderia_id) {
                        $mercaderia_existe = true;
                        $mercaderia_id = $mercaderias['mercaderia_id'];
                    }
                }
                if ($mercaderia_existe == true) {
                    $mercaderia = array_search($mercaderia_id, array_column($this->mercaderias_gastadas, 'mercaderia_id'));
                    $this->mercaderias_gastadas[$mercaderia]['cantidad'] = ($material->cantidad/$producto->unidades_por_caja) * $productos['cantidad'] ;
                } else {
                    $this->mercaderias_gastadas[] = ['mercaderia_id' => $mercaderia_id, "cantidad" => ($material->cantidad/$producto->unidades_por_caja) * $productos['cantidad']];
                }
            }
        }
    }

    public function sacarStock($productoId, $cantidad)
    {
        // Obtener las entradas de StockEntrante para el producto, ordenadas por ejemplo por fecha
        $entradas = StockMercaderiaEntrante::where('mercaderia_id', $productoId)->orderBy('created_at')->get();

        $newMercaderiaEntrante = StockMercaderiaEntrante::create([
            'mercaderia_id' => $productoId,
            'cantidad' => -$cantidad,
            'tipo' => 'Saliente',
        ]);

    }

    
    public function sumarStock($productoId, $cantidad){
        // Obtener las entradas de StockEntrante para el producto, ordenadas por ejemplo por fecha
        $entradas = StockMercaderiaEntrante::where('mercaderia_id', $productoId)->orderBy('created_at')->get();

        $newMercaderiaEntrante = StockMercaderiaEntrante::create([
            'mercaderia_id' => $productoId,
            'cantidad' => abs($cantidad),
            'tipo' => 'Entrante',
        ]);
    }

    public function enProduccion(){
        $orden = OrdenProduccion::find($this->identificador);
        $orden->update(['estado' => 2]);
        $this->alert('success', '¡Producción en proceso!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
            'timerProgressBar' => true,
        ]);
    }

    public function completarProduccion()
    {
        $Orden = OrdenProduccion::find($this->identificador);
        /*$this->sumarStock();*/
        $OrdenSave = $Orden->update(['estado' => 1]);
        if ($OrdenSave) {
            $this->alert('success', '¡Producción completada!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);


        } else {
            $this->alert('error', '¡No se ha podido cambiar al orden a completada!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

   /* public function sumarStock()
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
                    'orden_numero'=> $this->numero,
                    'stock_id' => $entrada->id,
                    'cantidad' => $producto->cajas_por_pallet,
                ]);
            }
        }
    }*/

    public function update()
    {
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
        $orden = OrdenProduccion::find($this->identificador);
        // Guardar datos validados
        $mercaderiaSave = $orden->update($validatedData);

        if ($this->estado_old == 0 && $this->estado == 1) {
            foreach ($this->mercaderias_gastadas as $mercaderiaIndex => $mercaderia) {
                $this->sacarStock($mercaderia['mercaderia_id'], $mercaderia['cantidad']);
            }

            /*$this->sumarStock();*/
        }
        foreach ($this->productos_ordenados as $mercaderiaIndex => $producto) {
            if ($producto['id'] != null) {
                $orden_producto = ProductosProduccion::find($producto['id']);
                $orden_producto->update(['orden_id' => $mercaderiaSave->id, 'producto_id' => $producto['producto_id'], 'cantidad' => $producto['cantidad']]);
            } else {
                ProductosProduccion::create(['orden_id' => $mercaderiaSave->id, 'producto_id' => $producto['producto_id'], 'cantidad' => $producto['cantidad']]);
            }
        }

        foreach ($this->mercaderias_gastadas as $mercaderiaIndex => $mercaderia) {
            if ($producto['id'] != null) {
                $orden_mercancia = MercaderiaProduccion::find($mercaderia['id']);
                $orden_mercancia->update(['orden_id' => $mercaderiaSave->id, 'mercaderia_id' => $mercaderia['mercaderia_id'], 'cantidad' => $mercaderia['cantidad']]);
            } else {
                MercaderiaProduccion::create(['orden_id' => $mercaderiaSave->id, 'mercaderia_id' => $mercaderia['mercaderia_id'], 'cantidad' => $mercaderia['cantidad']]);
            }
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


    public function submit(){
        //dd($this->productos_ordenados , $this->mercaderias_gastadas, $this->mercaderia_gastadas_old);

        $validatedData = $this->validate(
            [
                'numero' => 'required',
                'almacen_id' => 'required',
                'pedido_id' => 'nullable',
                'estado' => 'required',
                'fecha' => 'required',
                'observaciones' => 'nullable',
            ],
            // Mensajes de error
            [
                'almacen_id.required' => 'El numero de orden es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        $orden = OrdenProduccion::find($this->identificador);

        $orden->update($validatedData);


        //hay que diferenciar entre la mercaderia old y la nueva. Hay que sumar o restar la cantidad de la diferencia.
        //si la cantidad es positiva, se suma, si es negativa se resta.
        //si la cantidad es 0, no se hace nada.

        //primero se recorren las mercaderias viejas
        foreach ($this->mercaderia_gastadas_old as $mercaderiaIndex => $mercaderia) {
            //se busca la mercaderia en las nuevas
            $mercaderia_nueva = $this->mercaderias_gastadas[$mercaderiaIndex];
            //si la mercaderia no existe en las nuevas, se suma la cantidad a stock
            if($mercaderia_nueva == null){
                $this->sacarStock($mercaderia['mercaderia_id'], $mercaderia['cantidad']);
            }else{
                //si la mercaderia existe en las nuevas, se compara la cantidad
                if($mercaderia['cantidad'] > $mercaderia_nueva['cantidad']){
                    //si la cantidad es mayor, se resta la diferencia
                    $this->sumarStock($mercaderia['mercaderia_id'], $mercaderia['cantidad'] - $mercaderia_nueva['cantidad']);
                }else if($mercaderia['cantidad'] < $mercaderia_nueva['cantidad']){
                    //si la cantidad es menor, se suma la diferencia
                    $this->sacarStock($mercaderia['mercaderia_id'], $mercaderia_nueva['cantidad'] - $mercaderia['cantidad']);
                }
            }
        }

        //se recorren las mercaderias nuevas
        foreach ($this->mercaderias_gastadas as $mercaderiaIndex => $mercaderia) {
            //se busca la mercaderia en las viejas
            $mercaderia_vieja = $this->mercaderia_gastadas_old[$mercaderiaIndex];
            //si la mercaderia no existe en las viejas, se resta la cantidad a stock
            if($mercaderia_vieja == null){
                $this->sumarStock($mercaderia['mercaderia_id'], $mercaderia['cantidad']);
            }
        }

        //guardar los productos y las mercaderias
        foreach ($this->productos_ordenados as $producto) {
            if ($producto['id'] != null) {
                $orden_producto = ProductosProduccion::find($producto['id']);
                $orden_producto->update(['orden_id' => $orden->id, 'producto_id' => $producto['producto_id'], 'cantidad' => $producto['cantidad']]);
            } else {
                ProductosProduccion::create(['orden_id' => $orden->id, 'producto_id' => $producto['producto_id'], 'cantidad' => $producto['cantidad']]);
            }
        }

        foreach ($this->mercaderias_gastadas as $mercaderia) {
            if ($mercaderia['id'] != null) {
                $orden_mercancia = MercaderiaProduccion::find($mercaderia['id']);
                $orden_mercancia->update(['orden_id' => $orden->id, 'mercaderia_id' => $mercaderia['mercaderia_id'], 'cantidad' => $mercaderia['cantidad']]);
            } else {
                MercaderiaProduccion::create(['orden_id' => $orden->id, 'mercaderia_id' => $mercaderia['mercaderia_id'], 'cantidad' => $mercaderia['cantidad']]);
            }
        }



        //alert 

        $this->alert('success', '¡Orden de producción actualizada correctamente!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
            'timerProgressBar' => true,
        ]);


    }

    public function confirmed()
    {


        // Do something
        return redirect()->route('produccion.index');
    }

    public function updatePallet()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = $this->unidades_pallet_producto * $producto->cajas_por_pallet;
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;
    }
    public function updateCaja()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;
    }
    public function updateUnidad()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = floor($this->unidades_producto / $producto->unidades_por_caja);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
    }
    
    public function isPedidoMarketing($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);
        if ($pedido) {

            if($pedido->departamento_id == config('app.departamentos_pedidos')['Marketing']['id']){
                return true;
            }

        }
        return false;
    }
    public function destroy(){
        $orden = OrdenProduccion::find($this->identificador);
        $productos = ProductosProduccion::where('orden_id', $orden->id)->get();
        $mercaderias = MercaderiaProduccion::where('orden_id', $orden->id)->get();

        
        foreach ($this->mercaderias_gastadas as $mercaderiaIndex => $mercaderia) {
            $this->sumarStock($mercaderia['mercaderia_id'], $mercaderia['cantidad']);
        }

        foreach ($productos as $producto) {
            $producto->delete();
        }
        foreach ($mercaderias as $mercaderia) {
            $mercaderia->delete();
        }
        $orden->delete();
        $this->alert('success', '¡Orden de producción eliminada correctamente!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
            'timerProgressBar' => true,
        ]);
    }


}
