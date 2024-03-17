<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Almacen;
use App\Models\PedidosStatus;
use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\Clients;
use App\Models\Pedido;
use App\Mail\PedidoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Alertas;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EditComponent extends Component
{
    use LivewireAlert;
    public $identificador;
    public $porcentaje_descuento = 3; // Nuevo campo para el descuento personalizado
    public $cliente_id;
    public $nombre;
    public $precio;
    public $precioEstimado;
    public $precioSinDescuento;
    public $estado;
    public $estado_old;
    public $direccion_entrega;
    public $provincia_entrega;
    public $localidad_entrega;
    public $cod_postal_entrega;
    public $orden_entrega;
    public $fecha;
    public $observaciones;
    public $tipo_pedido_id;
    public $productos_pedido = [];
    public $productos_pedido_borrar = [];
    public $productos;
    public $clientes;
    public $descuento;
    public $unidades_producto;
    public $addProducto;
    public $producto_seleccionado;
    public $unidades_caja_producto;
    public $unidades_pallet_producto;
    public $precio_crema;
    public $precio_vodka07l;
    public $precio_vodka175l;
    public $precio_vodka3l;
    public $almacen_id;
    public $almacenes;
    public $bloqueado;
    public $porcentaje_bloq;

    public function mount()
    {
        $pedido = Pedido::find($this->identificador);
        $this->productos = Productos::all();
        $this->clientes = Clients::where('estado', 2)->get();
        $this->nombre = $pedido->nombre;
        $this->cliente_id = ltrim($pedido->cliente_id,0);
        $cliente = Clients::find($this->cliente_id);
        $this->nombre = $pedido->nombre;
        $this->estado = $pedido->estado;
        $this->estado_old = $pedido->estado;
        $this->almacenes = Almacen::all();
        $this->almacen_id = $pedido->almacen_id;
        $this->descuento = $pedido->descuento;
        $this->localidad_entrega = $cliente->localidadenvio;
        $this->provincia_entrega = $cliente->provinciaenvio;
        $this->direccion_entrega = $cliente->direccionenvio;
        $this->cod_postal_entrega = $cliente->codPostalenvio;
        $this->precio_crema = $cliente->precio_crema;
        $this->precio_vodka07l = $cliente->precio_vodka07l;
        $this->precio_vodka175l = $cliente->precio_vodka175l;
        $this->precio_vodka3l = $cliente->precio_vodka3l;
        $this->orden_entrega = $pedido->orden_entrega;
        $this->fecha = $pedido->fecha;
        $this->observaciones = $pedido->observaciones;
        $this->tipo_pedido_id = $pedido->tipo_pedido_id;
        $this->precio = $pedido->precio;
        $this->bloqueado = $pedido->bloqueado;
        $this->porcentaje_descuento = $pedido->porcentaje_descuento;
        $this->porcentaje_bloq = is_null($cliente->porcentaje_bloq) ? 10 : $cliente->porcentaje_bloq;
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->identificador)->get();
        foreach ($productos as $producto) {
            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_pedido_id' => $producto->producto_pedido_id,
                'unidades_old' => $producto->unidades,
                'precio_ud' => $producto->precio_ud,
                'precio_total' => $producto->precio_total,
                'unidades' => 0,
                'borrar' => 0,
            ];
        }
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');

    }


    public function selectCliente()
    {
        $cliente = Clients::find($this->cliente_id);
        $this->localidad_entrega = $cliente->localidad;
        $this->provincia_entrega = $cliente->provincia;
        $this->direccion_entrega = $cliente->direccion;
        $this->cod_postal_entrega = $cliente->cod_postal;
        $this->precio_crema = $cliente->precio_crema;
        $this->precio_vodka07l = $cliente->precio_vodka07l;
        $this->precio_vodka175l = $cliente->precio_vodka175l;
        $this->precio_vodka3l = $cliente->precio_vodka3l;
    }

    public function actualizarPrecioTotal($index)
    {
        $producto = $this->productos_pedido[$index];

        $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] *($producto['unidades'] + $producto['unidades_old']) ;
        $this->setPrecioEstimado();
    }
    protected $listeners = ['refreshComponent' => '$refresh'];


    public function render()
    {
        return view('livewire.pedidos.edit-component');
    }

    public function update()
    {
        if($this->porcentaje_descuento > $this->porcentaje_bloq){
            $this->bloqueado=true;
        }else{$this->bloqueado=false;}

        foreach ($this->productos_pedido as $productoPedido) {
            $producto = Productos::find($productoPedido['producto_pedido_id']);
            $precioBaseProducto = $this->obtenerPrecioPorTipo($producto->tipo_precio);

            // Compara el precio unitario del producto en el pedido con el precio base del cliente
            if ($productoPedido['precio_ud'] != $precioBaseProducto) {
                $this->bloqueado = true;
                break; // Si encuentra una modificación en los precios, no necesita seguir comprobando
            }
        }
        // Validación de datos
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'almacen_id' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento' => 'nullable',
                'porcentaje_descuento'=> 'nullable',
                'bloqueado'=> 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        $pedido = Pedido::find($this->identificador);
        // Guardar datos validados
        $pedidosSave = $pedido->update($validatedData);


        foreach ($this->productos_pedido as $productos) {
            if (!isset($productos['id'])) {
                DB::table('productos_pedido')->insert([
                    'producto_pedido_id' => $productos['producto_pedido_id'],
                    'pedido_id' => $this->identificador,
                    'unidades' => $productos['unidades'],
                    'precio_ud' => $productos['precio_ud'],
                    'precio_total' => $productos['precio_total']]);
            } else {
                if ($productos['unidades'] > 0) {
                    $unidades_finales = $productos['unidades_old'] + $productos['unidades'];
                    DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->update(['unidades' => $unidades_finales, 'precio_ud' => $productos['precio_ud']]);
                } else {
                    DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->update(['precio_ud' => $productos['precio_ud']]);
                }
            }
        }
        foreach ($this->productos_pedido_borrar as $productos) {
            if (isset($productos['id'])) {
                DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->delete();
            }
        }
        event(new \App\Events\LogEvent(Auth::user(), 4, $pedido->id));

        // Alertas de guardado exitoso
        if ($pedidosSave) {

            if( $this->bloqueado && $this->estado == 1){
                Alertas::create([
                    'user_id' => 13,
                    'stage' => 2,
                    'titulo' => 'Pedido Bloqueado: Pendiente de Aprobación',
                    'descripcion' => 'El pedido nº' . $pedido->id .' esta a la espera de aprobación',
                    'referencia_id' => $pedido->id,
                    'leida' => null,
                ]);}

            $this->alert('success', '¡Pedido registrado correctamente!', [
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

    public function getListeners()
    {
        return [
            'confirmed',
            'update',
            'alertaGuardar',
            'confirmDelete',
            'destroy',
            'imprimirPedido',
            'aceptarPedido',
            'rechazarPedido',
            'alertaAlmacen',
            'updateAlmacen',
            'checkLote'
        ];
    }

    public function updateAlmacen()
    {
        $this->estado = 4;
        // Validación de datos
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        $pedido = Pedido::find($this->identificador);
        // Guardar datos validados
        $pedidosSave = $pedido->update($validatedData);

        foreach ($this->productos_pedido as $productos) {
            if (!isset($productos['id'])) {
                DB::table('productos_pedido')->insert(['producto_pedido_id' => $productos['producto_pedido_id'], 'pedido_id' => $this->identificador, 'unidades' => $productos['unidades']]);
                /*$producto_stock = ProductoLote::find($productos['producto_pedido_id']);
                $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
                $producto_stock->update(['cantidad_actual' => $cantidad_actual]);*/
            } else {
                if ($productos['unidades'] > 0) {
                    $unidades_finales = $productos['unidades_old'] + $productos['unidades'];
                    DB::table('productos_pedido')->find($productos['id'])->update(['unidades' => $unidades_finales]);
                   /* $producto_stock = ProductoLote::find($productos['producto_pedido_id']);
                    $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
                    $producto_stock->update(['cantidad_actual' => $cantidad_actual]);*/
                }
            }
        }
        event(new \App\Events\LogEvent(Auth::user(), 4, $pedido->id));

        // Alertas de guardado exitoso
        if ($pedidosSave) {
            $this->alert('success', '¡Pedido enviado a Almacén!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function alertaGuardar()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el Pedido? No hay vuelta atrás', [
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

    public function aceptarPedido()
    {
        if($this->porcentaje_descuento > $this->porcentaje_bloq){
            $this->bloqueado=true;
        }else{$this->bloqueado=false;}

        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'almacen_id' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento' => 'nullable',
                'porcentaje_descuento'=> 'nullable',
                'bloqueado'=> 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );
        $pedido = Pedido::find($this->identificador);
        $pedido->update($validatedData);
        if($this->bloqueado=true){
            return;
        }
        $pedidosSave = $pedido->update(['estado' => 2]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Aceptado en Almacén',
                'descripcion' => 'El pedido nº ' . $pedido->id.' ha sido aceptado',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);
            $this->alert('success', '¡Pedido aceptado!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function rechazarPedido()
    {
        $pedido = Pedido::find($this->identificador);
        $pedidosSave = $pedido->update(['estado' => 7]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Rechazado ',
                'descripcion' => 'El pedido nº ' . $pedido->id . ' ha sido rechazado',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);
            $this->alert('success', '¡Pedido rechazado!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function alertaAceptar()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'aceptarPedido',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    public function alertaRechazar()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'rechazarPedido',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
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
    public function alertaAlmacen()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de proceder.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'updateAlmacen',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }
    public function getUnidadesTabla($id)
    {
        $producto = Productos::find($this->productos_pedido[$id]['producto_pedido_id']);
        if (isset($this->productos_pedido[$id]['unidades_old'])) {
            $uds_total = $this->productos_pedido[$id]['unidades_old'] + $this->productos_pedido[$id]['unidades'];
            $cajas = ($uds_total / $producto->unidades_por_caja);
            $pallets = floor($cajas / $producto->cajas_por_pallet);
            $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
            $unidades = '';
            if ($cajas_sobrantes > 0) {
                $unidades = $uds_total . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
            } else {
                $unidades = $uds_total . ' unidades (' . $pallets . ' pallets)';
            }
        } else {
            $cajas = ($this->productos_pedido[$id]['unidades'] / $producto->unidades_por_caja);
            $pallets = floor($cajas / $producto->cajas_por_pallet);
            $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
            $unidades = '';
            if ($cajas_sobrantes > 0) {
                $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
            } else {
                $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets)';
            }
        }

        return $unidades;
    }

    public function deleteArticulo($id)
    {

        $this->productos_pedido_borrar[] = $this->productos_pedido[$id];
        unset($this->productos_pedido[$id]);
        $this->productos_pedido = array_values($this->productos_pedido);
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }
    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }
    public function addProductos($id)
    {
        $producto = Productos::find($id);
        if (!$producto) {
            $this->alert('error', 'Producto no encontrado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }

        $precioUnitario = $this->obtenerPrecioPorTipo($producto->tipo_precio);
        $precioTotal = $precioUnitario * $this->unidades_producto;

        $producto_existe = false;
        foreach ($this->productos_pedido as &$productoPedido) {
            if ($productoPedido['producto_pedido_id'] == $id) {
                $producto_existe = true;
                $productoPedido['unidades'] += $this->unidades_producto;  // Actualiza la cantidad
                $productoPedido['precio_ud'] = $precioUnitario;
                $productoPedido['precio_total'] += $precioTotal;  // Actualiza el precio total
                break;
            }
        }

        if (!$producto_existe) {
            $this->productos_pedido[] = [
                'producto_pedido_id' => $id,
                'unidades' => $this->unidades_producto,
                'precio_ud' => $precioUnitario,
                'precio_total' => $precioTotal
            ];
        }

        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }
    private function obtenerPrecioPorTipo($tipoPrecio)
    {
        switch ($tipoPrecio) {
            case 1:
                return $this->precio_crema;
            case 2:
                return $this->precio_vodka07l;
            case 3:
                return $this->precio_vodka175l;
            case 4:
                return $this->precio_vodka3l;
            default:
                return 0;
        }
    }


    public function setPrecioEstimado()
    {
        $this->precioEstimado = 0;
        foreach ($this->productos_pedido as $producto) {
            $this->precioEstimado += $producto['precio_total'];
        }
        $this->precioSinDescuento = $this->precioEstimado;
        // Verificar si el descuento está activado
        if ($this->descuento) {
            // Calcular el 3% de descuento del precio total
            //$descuento = $this->descuento_personalizado ?? ($this->precioEstimado * 0.03);
            $descuento = $this->precioEstimado * ($this->porcentaje_descuento / 100);

            // Aplicar el descuento al precio total
            $this->precioEstimado -= $descuento;
        }

        // Asignar el precio final
        $this->precio = number_format($this->precioEstimado, 2, '.', '');
    }



    public function getProductoNombre()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->nombre != null) {
            return $producto->nombre;
        }
    }
    public function getProductoPrecio()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->precio != null) {
            return $producto->precio . "€ (Sin IVA)";
        }
    }
    public function getProductoPrecioIVA()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->precio != null) {
            return ($producto->precio + ($producto->precio * ($producto->iva / 100))) . "€ (Con IVA)";
        }
    }
    public function getProductoUds()
    {
        if ($this->lote_seleccionado != null) {
            $producto = ProductoLote::find($this->lote_seleccionado);
            if ($producto != null && $producto->cantidad_actual != null) {
                foreach ($this->productos_pedido as $productos) {
                    if ($productos['producto_pedido_id'] == $this->lote_seleccionado) {
                        return ($producto->cantidad_actual - $this->unidades_producto - $productos["unidades"]);
                    }
                }
                return ($producto->cantidad_actual - $this->unidades_producto);
            }
        }
    }
    public function getProductoImg()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null) {
            return asset('storage/photos/' . $producto->foto_ruta);
        }

        $this->emit('refreshComponent');
    }


    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('pedidos.index');
    }

    public function getEstadoNombre()
    {
        return PedidosStatus::firstWhere('id', $this->estado)->status;
    }

    public function confirmDelete()
    {
        $pedidoId = $this->identificador;

        // Primero, elimina todos los productos_pedido asociados con este pedido
        DB::table('productos_pedido')->where('pedido_id', $pedidoId)->delete();

        // Luego, elimina el pedido
        $pedido = Pedido::find($pedidoId);
        if ($pedido) {
            event(new \App\Events\LogEvent(Auth::user(), 10, $pedido->id));
            $pedido->delete();
        }

        return redirect()->route('pedidos.index');

    }
    public function imprimirPedido()
    {
    $pedido = Pedido::find($this->identificador);
    if (!$pedido) {
        abort(404, 'Pedido no encontrado');
    }

    $cliente = Clients::find($pedido->cliente_id);
    $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

    // Preparar los datos de los productos del pedido
    $productos = [];
    foreach ($productosPedido as $productoPedido) {
        $producto = Productos::find($productoPedido->producto_pedido_id);
        if ($producto) {
            $productos[] = [
                'nombre' => $producto->nombre,
                'cantidad' => $productoPedido->unidades,
                'precio_ud' => $productoPedido->precio_ud,
                'precio_total' => $productoPedido->precio_total,
            ];
        }
    }

    $datos = [
        'pedido' => $pedido,
        'cliente' => $cliente,
        'localidad_entrega' => $pedido->localidad_entrega,
        'direccion_entrega' => $pedido->direccion_entrega,
        'cod_postal_entrega' => $pedido->cod_postal_entrega,
        'provincia_entrega' => $pedido->provincia_entrega,
        'fecha' => $pedido->fecha,
        'observaciones' => $pedido->observaciones,
        'precio' => $pedido->precio,
        'descuento' => $pedido->descuento,
        'productos' => $productos,
    ];

    $pdf = PDF::loadView('livewire.pedidos.pdf-component', $datos)->setPaper('a4', 'vertical')->output();
    Mail::to($cliente->email)->send(new PedidoMail($pdf, $cliente,$pedido,$productos));
    /*--return response()->streamDownload(
        fn () => print($pdf),
        "pedido_{$pedido->id}.pdf"
    );*/
}

}
