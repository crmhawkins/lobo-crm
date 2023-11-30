<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\PedidosStatus;
use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\Clients;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EditComponent extends Component
{
    use LivewireAlert;
    public $identificador;
    public $cliente_id;
    public $nombre;
    public $precio;
    public $precioEstimado;
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

    public function mount()
    {
        $pedido = Pedido::find($this->identificador);
        $this->productos = Productos::all();
        $this->clientes = Clients::all();
        $this->nombre = $pedido->nombre;
        $this->cliente_id = $pedido->cliente_id;
        $this->nombre = $pedido->nombre;
        $this->estado = $pedido->estado;
        $this->estado_old = $pedido->estado;
        $this->direccion_entrega = $pedido->direccion_entrega;
        $this->provincia_entrega = $pedido->provincia_entrega;
        $this->localidad_entrega = $pedido->localidad_entrega;
        $this->cod_postal_entrega = $pedido->cod_postal_entrega;
        $this->orden_entrega = $pedido->orden_entrega;
        $this->fecha = $pedido->fecha;
        $this->observaciones = $pedido->observaciones;
        $this->tipo_pedido_id = $pedido->tipo_pedido_id;
        $this->precio = $pedido->precio;
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->identificador)->get();
        foreach ($productos as $producto) {
            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_lote_id' => $producto->producto_lote_id,
                'unidades_old' => $producto->unidades,
                'precio_ud' => $producto->precio_ud,
                'unidades' => 0,
                'borrar' => 0,
            ];
        }
    }
    protected $listeners = ['refreshComponent' => '$refresh'];


    public function render()
    {
        return view('livewire.pedidos.edit-component');
    }

    public function update()
    {

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
                'descuento' => 'nullable',
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
                DB::table('productos_pedido')->insert(['producto_lote_id' => $productos['producto_lote_id'], 'pedido_id' => $this->identificador, 'unidades' => $productos['unidades'], 'precio_ud' => $productos['precio_ud']]);
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
                DB::table('productos_pedido')->insert(['producto_lote_id' => $productos['producto_lote_id'], 'pedido_id' => $this->identificador, 'unidades' => $productos['unidades']]);
                $producto_stock = ProductoLote::find($productos['producto_lote_id']);
                $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
                $producto_stock->update(['cantidad_actual' => $cantidad_actual]);
            } else {
                if ($productos['unidades'] > 0) {
                    $unidades_finales = $productos['unidades_old'] + $productos['unidades'];
                    DB::table('productos_pedido')->find($productos['id'])->update(['unidades' => $unidades_finales]);
                    $producto_stock = ProductoLote::find($productos['producto_lote_id']);
                    $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
                    $producto_stock->update(['cantidad_actual' => $cantidad_actual]);
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

    public function aceptarPedido()
    {
        $pedido = Pedido::find($this->identificador);
        $pedidosSave = $pedido->update(['estado' => 2]);
        if ($pedidosSave) {
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
        $pedidosSave = $pedido->update(['estado' => 3]);
        if ($pedidosSave) {
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
        $producto = Productos::find($this->productos_pedido[$id]['producto_lote_id']);
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
    }
    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }
    public function addProductos($id)
    {
        $producto_existe = false;
        $producto_id = $id;
        foreach ($this->productos_pedido as $productos) {
            if ($productos['producto_lote_id'] == $id) {
                $producto_existe = true;
                $producto_id = $productos['producto_lote_id'];
            }
        }
        if ($producto_existe == true) {
            $producto = array_search($producto_id, array_column($this->productos_pedido, 'producto_lote_id'));
            $this->productos_pedido[$producto]['unidades'] = $this->productos_pedido[$producto]['unidades'] + $this->unidades_producto;
            if (!isset($this->productos_pedido[$producto]['precio_ud'])) {
                $this->productos_pedido[$producto]['precio_ud'] = 0;
            }
        } else {
            $this->productos_pedido[] = ['producto_lote_id' => $id, "unidades" => $this->unidades_producto, "precio_ud" => 0];
        }

        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }

    public function setPrecioEstimado()
    {
        $this->precioEstimado = 0;
        foreach ($this->productos_pedido as $productos) {
            $this->precioEstimado += ($productos['precio_ud']);
        }
        $this->precio = $this->precioEstimado - $this->descuento;
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
                    if ($productos['producto_lote_id'] == $this->lote_seleccionado) {
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
}
