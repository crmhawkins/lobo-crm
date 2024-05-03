<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Productos;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\ProductoLote;
use App\Models\Alertas;
use App\Models\PedidosStatus;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AnotacionesClientePedido;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $almacen_id = 0;
    public $porcentaje_descuento = 3; // Nuevo campo para el descuento personalizado
    public $cliente_id;
    public $nombre;
    public $precio = 0;
    public $precioEstimado = 0;
    public $precioSinDescuento;
    public $estado = 1;
    public $direccion_entrega;
    public $provincia_entrega;
    public $localidad_entrega;
    public $cod_postal_entrega;
    public $orden_entrega;
    public $fecha;
    public $observaciones;
    public $tipo_pedido_id = 1;
    public $productos_pedido = [];
    public $productos;
    public $descuento = 0;
    public $clientes;
    public $unidades_producto = 0;
    public $addProducto = 0;
    public $producto_seleccionado;
    public $unidades_pallet_producto;
    public $unidades_caja_producto;
    public $precio_crema;
    public $precio_vodka07l;
    public $precio_vodka175l;
    public $precio_vodka3l;
    public $bloqueado;
    public $porcentaje_bloq;
    public $porcentaje_sincargo = 0;
	public $sinCargo = false;
    public $anotacionesProximoPedido = [];

    public function mount()
    {

        $this->productos = Productos::all();
        $this->clientes = Clients::where('estado', 2)->get();
        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->estado = 1;
        $this->cliente_id = null;
    }

    public function selectCliente()
    {
        $cliente = Clients::find($this->cliente_id);
        $this->localidad_entrega = $cliente->localidadenvio;
        $this->provincia_entrega = $cliente->provinciaenvio;
        $this->direccion_entrega = $cliente->direccionenvio;
        $this->cod_postal_entrega = $cliente->codPostalenvio;
        $this->precio_crema = $cliente->precio_crema;
        $this->precio_vodka07l = $cliente->precio_vodka07l;
        $this->precio_vodka175l = $cliente->precio_vodka175l;
        $this->precio_vodka3l = $cliente->precio_vodka3l;
        $this->porcentaje_bloq = $cliente->porcentaje_bloq;

        $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->cliente_id)->where('estado', 'pendiente')->get();
        //alert si hay anotaciones pendientes con botón para cerrar y boton para ver anotaciones
        if (count($this->anotacionesProximoPedido) > 0) {
            $this->alert('info', '¡El cliente tiene anotaciones pendientes!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Cerrar',
                //'showDenyButton' => true,
                //'denyButtonText' => 'Ver anotaciones',
                'onConfirmed' => '',
                //'onDenied' => 'verAnotaciones',
                'timerProgressBar' => true,
            ]);
        }
       

    }


   

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        return view('livewire.pedidos.create-component');
    }
    // Al hacer submit en el formulario
    public function submit()
    {
        $totalUnidades = 0;
        $totalUnidadesSinCargo = 0;
        foreach ($this->productos_pedido as $productoPedido) {
            $totalUnidades += $productoPedido['unidades'];
            if ($productoPedido['precio_ud'] == 0) {
                $totalUnidadesSinCargo += $productoPedido['unidades'];
            }
        }

        if ($totalUnidades > 0) {
            $this->porcentaje_sincargo = ($totalUnidadesSinCargo / $totalUnidades) * 100;
        }

        if($this->porcentaje_sincargo > $this->porcentaje_bloq){
            $this->bloqueado=true;
        }else{$this->bloqueado=false;}

         foreach ($this->productos_pedido as $productoPedido) {
             $producto = Productos::find($productoPedido['producto_pedido_id']);
             $precioBaseProducto = $this->obtenerPrecioPorTipo($producto);

            // Compara el precio unitario del producto en el pedido con el precio base del cliente
            if ($productoPedido['precio_ud'] != $precioBaseProducto && $productoPedido['precio_ud'] != 0) {
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
                'almacen_id' => 'required',
                'observaciones' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento'=> 'nullable',
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

        // Guardar datos validados
        $pedidosSave = Pedido::create($validatedData);

        if( $this->bloqueado){
        Alertas::create([
            'user_id' => 13,
            'stage' => 2,
            'titulo' => 'Pedido Bloqueado: Pendiente de Aprobación',
            'descripcion' => 'El pedido nº ' . $pedidosSave->id.' esta a la espera de aprobación',
            'referencia_id' => $pedidosSave->id,
            'leida' => null,
        ]);}

            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Recibido',
                'descripcion' => 'El pedido nº ' . $pedidosSave->id.' ha sido recibido',
                'referencia_id' => $pedidosSave->id,
                'leida' => null,
            ]);

        foreach ($this->productos_pedido as $productos) {
            DB::table('productos_pedido')->insert([
                'producto_pedido_id' => $productos['producto_pedido_id'],
                'pedido_id' => $pedidosSave->id,
                'unidades' => $productos['unidades'],
                'precio_ud' => $productos['precio_ud'],
                'precio_total' => $productos['precio_total']
            ]);

        }
        event(new \App\Events\LogEvent(Auth::user(), 3, $pedidosSave->id));

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

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'alertaGuardar',
            'checkLote',
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

    public function updatePallet()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = $this->unidades_pallet_producto * $producto->cajas_por_pallet;
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;
    }
    public function updateCaja()
    {
        /*$producto = Productos::find($this->producto_seleccionado);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;*/
		 $producto = Productos::find($this->producto_seleccionado);

		// Ensure that $this->unidades_caja_producto and $producto->cajas_por_pallet are treated as integers.
		$cajasPorPallet = (int)$producto->cajas_por_pallet;
		$unidadesCajaProducto = (int)$this->unidades_caja_producto;

		// Calculate unidades_pallet_producto by dividing unidades_caja_producto by cajas_por_pallet.
		// Both operands are cast to integers to prevent type mismatch errors.
		$this->unidades_pallet_producto = floor($unidadesCajaProducto / $cajasPorPallet);

		// Ensure that $producto->unidades_por_caja is treated as an integer.
		$unidadesPorCaja = (int)$producto->unidades_por_caja;

		// Calculate unidades_producto by multiplying unidades_caja_producto by unidades_por_caja.
		// The operands are cast to integers to ensure proper arithmetic operation.
		$this->unidades_producto = $unidadesCajaProducto * $unidadesPorCaja;
    }
    public function updateUnidad()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = floor($this->unidades_producto / $producto->unidades_por_caja);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
    }

    public function deleteArticulo($id)
    {
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
    public function getUnidadesTabla($id)
    {
        $producto = Productos::find($this->productos_pedido[$id]['producto_pedido_id']);
        $cajas = ($this->productos_pedido[$id]['unidades'] / $producto->unidades_por_caja);
        $pallets = floor($cajas / $producto->cajas_por_pallet);
        $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
        $unidades = '';
        if ($cajas_sobrantes > 0) {
            $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
        } else {
            $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets)';
        }
        return $unidades;
    }

    public function addProductos($id)
    {
        $producto = Productos::find($id);
        if (!$producto) {
            // Muestra una alerta al usuario indicando que el producto no se encontró
            $this->alert('error', 'Producto no encontrado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }

        $precioUnitario = $this->obtenerPrecioPorTipo($producto);
        $precioTotal = $precioUnitario * $this->unidades_producto;

        $producto_existe = false;
        $producto_existe_sincargo =false;
        foreach ($this->productos_pedido as $productoPedido) {
            if ($productoPedido['producto_pedido_id'] == $id) {
                if ($productoPedido['precio_ud'] !== 0) {
                $producto_existe = true;
                break;
                }else{
                $producto_existe_sincargo = true;
                }
            }
        }
		if($this->sinCargo == true){
            if ($producto_existe_sincargo) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $id) {
                        if ($productoPedido['precio_ud'] == 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
			} else {
			$this->productos_pedido[] = [
                'producto_pedido_id' => $id,
                'unidades' => $this->unidades_producto,
                'precio_ud' => 0,
                'precio_total' => 0
            ];}

		} else{


			if ($producto_existe) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $id) {
                        if ($productoPedido['precio_ud'] !== 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
				$this->productos_pedido[$key]['precio_ud'] = $precioUnitario;
				$this->productos_pedido[$key]['precio_total'] += $precioTotal;
			} else {
				$this->productos_pedido[] = [
					'producto_pedido_id' => $id,
					'unidades' => $this->unidades_producto,
					'precio_ud' => $precioUnitario,
					'precio_total' => $precioTotal
				];
			}

		}

        $this->sinCargo = false;

        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->unidades_caja_producto = 0;
        $this->unidades_pallet_producto = 0;
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }

    public function actualizarPrecioTotal($index)
    {
        $producto = $this->productos_pedido[$index];
        if(isset($producto['precio_ud']) && isset($producto['unidades'])) {
            $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['unidades'];
        }
        $this->setPrecioEstimado();
    }
    private function obtenerPrecioPorTipo($producto)
    {
        $tipoPrecio = $producto->tipo_precio;
        switch ($tipoPrecio) {
            case 1:
                return $this->precio_crema;
            case 2:
                return $this->precio_vodka07l;
            case 3:
                return $this->precio_vodka175l;
            case 4:
                return $this->precio_vodka3l;
            case 5:
                return $producto->precio;
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

    public function getProductoImg()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null) {
            return asset('storage/photos/' . $producto->foto_ruta);
        }

        $this->emit('refreshComponent');
    }


    public function confirmed()
    {
        return redirect()->route('pedidos.index');
    }

    public function getEstadoNombre()
    {
        return PedidosStatus::firstWhere('id', $this->estado)->status;
    }
}
