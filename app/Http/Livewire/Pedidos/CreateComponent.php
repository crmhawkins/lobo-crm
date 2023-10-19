<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Productos;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\ProductoLote;
use App\Models\PedidosStatus;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $cliente_id = 1;
    public $nombre;
    public $precio = 0;
    public $precioEstimado = 0;
    public $estado = 'Pendiente';
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
    public $lotes;
    public $clientes;
    public $unidades_producto = 0;
    public $addProducto = 0;
    public $producto_seleccionado;
    public $lote_seleccionado;

    public function mount()
    {
        $this->productos = Productos::all();
        $this->lotes = ProductoLote::all();
        $this->clientes = Clients::all();
        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->estado = 1;
    }
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        return view('livewire.pedidos.create-component');
    }
    // Al hacer submit en el formulario
    public function submit()
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

        foreach ($this->productos_pedido as $productos) {
            DB::table('productos_pedido')->insert(['producto_lote_id' => $productos['producto_lote_id'], 'pedido_id' => $pedidosSave->id, 'unidades' => $productos['unidades'], 'precio_ud' => $productos['precio_ud']]);
            $producto_stock = ProductoLote::find($productos['producto_lote_id']);
            $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
            $producto_stock->update(['cantidad_actual' => $cantidad_actual]);
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

    public function checkLote()
    {
        if ($this->producto_seleccionado == null) {
            $this->lote_seleccionado == null;
        } else {
            $this->lote_seleccionado = ProductoLote::where('producto_id', $this->producto_seleccionado)->first()->id;
        }
    }

    public function getNombreTabla($id)
    {
        $producto_id = $this->lotes->where('id', $id)->first()->producto_id;
        $nombre_producto = $this->productos->where('id', $producto_id)->first()->nombre;
        return $nombre_producto;
    }
    public function getNombreLoteTabla($id)
    {
        $producto_id = $this->lotes->where('id', $id)->first()->lote_id;
        return $producto_id;
    }

    public function addProductos($id)
    {
        $producto = ProductoLote::find($id);
        $producto_existe = false;
        $producto_id = 0;
        if ($this->unidades_producto < $producto->cantidad_actual) {
            foreach ($this->productos_pedido as $productos) {
                if ($productos['producto_lote_id'] == $id) {
                    $producto_existe = true;
                    $producto_id = $productos['producto_lote_id'];
                }
            }
            if ($producto_existe == true) {
                $producto = array_search($producto_id, array_column($this->productos_pedido, 'producto_lote_id'));
                $this->productos_pedido[$producto]['unidades'] = $this->productos_pedido[$producto]['unidades'] + $this->unidades_producto;
            } else {
                $this->productos_pedido[] = ['producto_lote_id' => $id, "unidades" => $this->unidades_producto, "precio_ud" => 0];
            }
        } else {
            $this->alert('warning', 'No hay unidades disponibles suficientes para la petición solicitada.');
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
            $this->precioEstimado += ($productos['precio_ud'] * $productos['unidades']);
        }
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
        $lote = ProductoLote::find($this->lote_seleccionado);
        if ($this->unidades_producto < $lote->cantidad_actual) {
            return asset('storage/photos/' . $producto->foto_ruta);
        }

        $this->unidades_producto = 0;
        $this->emit('refreshComponent');
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('pedidos.index');
    }

    public function getEstadoNombre(){
        return PedidosStatus::firstWhere('id', $this->estado)->status;
    }
}
