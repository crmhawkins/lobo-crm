<?php

namespace App\Http\Livewire\Comercial;

use App\Models\Productos;
use App\Models\ClientesComercial;
use App\Models\PedidosComercial;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Iva;
use App\Models\User;
use App\Models\ProductosPedidoComercial;

use Illuminate\Support\Facades\Mail;

class createpedido extends Component
{
    use LivewireAlert;
    public $cliente_id;
    public $nombre;
    public $precio = 0;
    public $precioEstimado = 0;
    public $estado = 1;
    public $observaciones;
    public $productos_pedido = [];
    public $productos;
    public $clientes;
    public $unidades_producto = 0;
    public $addProducto = 0;
    public $producto_seleccionado;
    public $unidades_pallet_producto;
    public $unidades_caja_producto;

    public $subtotal = 0;
    public $iva_total = 0;
    public $npedido;

    public $fecha;

    public $direccion_entrega;
    public $localidad_entrega;
    public $provincia_entrega;
    public $cod_postal_entrega;
    public $sinCargo;
    public $iva;


    public function mount()
    {


       // Inicializar la colección de productos
        // Obtener todos los productos, ordenados por grupo y orden dentro del grupo
        $this->productos = Productos::orderByRaw("CASE WHEN orden IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'orden' al final
        ->orderBy('orden', 'asc')  // Ordenar primero por orden
        ->orderByRaw("CASE WHEN grupo IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'grupo' al final
        ->orderBy('grupo', 'asc')  // Luego ordenar por grupo
        ->orderBy('nombre', 'asc')  // Finalmente, ordenar alfabéticamente por nombre
        ->get();

        $this->clientes = ClientesComercial::all();
        //si el usuario autenticado es comercial, solo ve sus clientes asociados.
        if (Auth::user()->role == 3 ){
            $this->clientes = ClientesComercial::where('comercial_id', Auth::user()->id)->get();
        }

        $this->cliente_id = null;
    }



    public function selectCliente()
    {
        $cliente = ClientesComercial::find($this->cliente_id);
        $this->cliente = $cliente;
        $this->direccion_entrega = $cliente->direccion;
        $this->localidad_entrega = $cliente->localidad;
        $this->provincia_entrega = $cliente->provincia;
        $this->cod_postal_entrega = $cliente->cod_postal;
    }



    protected $listeners = ['refreshComponent' => '$refresh', 'closeModal' => 'closeModal'];

    public function render()
    {
        return view('livewire.comercial.createpedido');
    }




    // Al hacer submit en el formulario
    public function submit()
    {

        $totalUnidades = 0;
        $totalUnidadesSinCargo = 0;
        foreach ($this->productos_pedido as $productoPedido) {
            $totalUnidades += $productoPedido['cantidad'];
            if ($productoPedido['precio_ud'] == 0) {
                $totalUnidadesSinCargo += $productoPedido['cantidad'];
            }
        }




        $total_iva  = 0;
        foreach ($this->productos_pedido as $productoPedido) {
             $producto = Productos::find($productoPedido['producto_id']);
             $precioBaseProducto = $this->obtenerPrecioPorTipo($producto);
             //ver que iva tiene el producto
                $iva = Iva::find($producto->iva_id);
                if($iva){
                        $total_iva += (($productoPedido['precio_ud'] * $productoPedido['cantidad'])) * ($iva->iva / 100);
                }else{
                    $total_iva += (($productoPedido['precio_ud'] * $productoPedido['cantidad'])) * (21 / 100);
                }


         }


         $this->iva_total = $total_iva;

         $this->total = $this->subtotal + $this->iva_total;

        // Validación de datos
        //si el rol es 2

            $validatedData = $this->validate(
                [
                    'cliente_id' => 'required',

                ],
                // Mensajes de error
                [
                    'cliente_id.required' => 'El cliente es obligatorio.',

                ]
            );



        // Guardar datos validados
        $pedidosSave = PedidosComercial::create([
            'cliente_id' => $this->cliente_id,
            'npedido' => $this->npedido,
            'comercial_id' => Auth::user()->id,
            'fecha' => $this->fecha,
            'precio' => $this->precio,
            'iva' => $this->iva_total,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'direccion_entrega' => $this->direccion_entrega,
            'localidad_entrega' => $this->localidad_entrega,
            'cod_postal_entrega' => $this->cod_postal_entrega,
            'provincia_entrega' => $this->provincia_entrega,
            'observaciones' => $this->observaciones,
        ]);

        foreach ($this->productos_pedido as $productos) {

            ProductosPedidoComercial::create([
                'pedido_id' => $pedidosSave->id,
                'producto_id' => $productos['producto_id'],
                'cantidad' => $productos['cantidad'],
                'precio_ud' => $productos['precio_ud'],
                'precio_total' => $productos['precio_total']
            ]);

        }

            if ($pedidosSave) {

                $this->alert('success', '¡Pedido registrado correctamente!', [
                    'position' => 'center',
                    'timer' => null,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'confirmed',
                    'confirmButtonText' => 'ok',
                    'timerProgressBar' => false,
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
            'closeModal',
            'domLoaded',
            'isOnline',
            'isNotOnline'
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

    public function updated($property){
        if($property == 'precio' ){

            //controlar valores no numericos

            if(!is_numeric($this->precio)){
              //comprobar si ha metido un numero con coma y cambiarlo a punto
                if(strpos($this->precio, ',') !== false){
                    $this->precio = str_replace(',', '.', $this->precio);
                    //comprobar si ahora no es un numero y tirar alerta si no lo es
                    if(!is_numeric($this->precio)){
                        //alerta debe introducir un valor numerico
                        $this->alert('error', '¡Debe introducir un valor numérico!', [
                            'position' => 'center',
                            'timer' => 1500,
                            'toast' => false,
                            'showConfirmButton' => false,
                            'timerProgressBar' => true,
                            'onClose' => $this->emit('closeModal'),
                            'allowOutsideClick' => false,
                        ]);
                        $this->precio = 0;
                    }
                }else{
                    //alerta debe introducir un valor numerico
                    $this->alert('error', '¡Debe introducir un valor numérico!', [
                        'position' => 'center',
                        'timer' => 1500,
                        'toast' => false,
                        'showConfirmButton' => false,
                        'timerProgressBar' => true,
                        'onClose' => $this->emit('closeModal'),
                        'allowOutsideClick' => false,
                    ]);
                    $this->precio = 0;
                }
            }
        }
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
        $producto = Productos::find($this->productos_pedido[$id]['producto_id']);
        $cajas = ($this->productos_pedido[$id]['cantidad'] / $producto->unidades_por_caja);
        $pallets = floor($cajas / $producto->cajas_por_pallet);
        $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
        $unidades = '';
        if ($cajas_sobrantes > 0) {
            $unidades = $this->productos_pedido[$id]['cantidad'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
        } else {
            $unidades = $this->productos_pedido[$id]['cantidad'] . ' unidades (' . $pallets . ' pallets)';
        }
        return $unidades;
    }


    public function isClienteSeleccionado(){
        if($this->cliente_id == null){

            //alert cuando finalize que ejecute closeModal
            $this->alert('error', '¡Debe seleccionar un cliente!', [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
                'onClose' => $this->emit('closeModal'),
                'allowOutsideClick' => false,
            ]);


            return false;
        }
        return true;
    }

    public function closeModal(){
        return '';
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
            if ($productoPedido['producto_id'] == $id) {
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
                    if ($productoPedido['producto_id'] == $id) {
                        if ($productoPedido['precio_ud'] == 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['cantidad'] += $this->unidades_producto;
			} else {
			$this->productos_pedido[] = [
                'producto_id' => $id,
                'cantidad' => $this->unidades_producto,
                'precio_ud' => 0,
                'precio_total' => 0
            ];}

		} else{


			if ($producto_existe) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_id'] == $id) {
                        if ($productoPedido['precio_ud'] !== 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['cantidad'] += $this->unidades_producto;
				$this->productos_pedido[$key]['precio_ud'] = $precioUnitario;
				$this->productos_pedido[$key]['precio_total'] += $precioTotal;
			} else {
				$this->productos_pedido[] = [
					'producto_id' => $id,
					'cantidad' => $this->unidades_producto,
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
        if(isset($producto['precio_ud']) && isset($producto['cantidad'])) {
            $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['cantidad'];
        }
        $this->setPrecioEstimado();
    }
    private function obtenerPrecioPorTipo($producto)
    {

        return $producto->precio ?? 0;
    }

    public function setPrecioEstimado()
    {
        $this->precioEstimado = 0;


        foreach ($this->productos_pedido as $producto) {
            $this->precioEstimado += $producto['precio_total'];
        }



        // Asignar el precio final
        $this->precio = number_format($this->precioEstimado, 2, '.', '');
        $this->subtotal = number_format($this->precioEstimado,2, '.', '');
        $this->total = $this->precioEstimado * 0.21;
        $this->iva = $this->total;
        $this->total = $this->precioEstimado + $this->iva;
        $this->total = number_format($this->total, 2, '.', '');
        $this->iva = number_format($this->iva, 2, '.', '');

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
        return redirect()->route('comercial.pedidos');
    }

    public function getEstadoNombre()
    {
        return PedidosStatus::firstWhere('id', $this->estado)->status;
    }
}
