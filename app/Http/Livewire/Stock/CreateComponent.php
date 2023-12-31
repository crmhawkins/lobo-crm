<?php

namespace App\Http\Livewire\Stock;

use App\Models\Productos;
use App\Models\ProductosCategories;
use App\Models\Stock;
use App\Models\StockEntrante;
use App\Models\Almacen;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $qr_id;
    public $numero;
    public $precio = 0;
    public $estado;
    public $fecha;
    public $observaciones;
    public $producto_seleccionado;
    public $unidades_producto;
    public $almacenes;
    public $almacen_id;
    public $productos_pedido = [];
    public $productos;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->estado = 0;
        $this->qr_id = $this->identificador;
        $this->productos = Productos::all();
        $this->almacenes = Almacen::all();
        $user = Auth::user();
        $this->almacen_id = $user->almacen_id;
    }

    public function render()
    {
        return view('livewire.stock.create-component');
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

    public function deleteArticulo($id)
    {
        unset($this->productos_pedido[$id]);
        $this->productos_pedido = array_values($this->productos_pedido);
    }

    public function addProducto($id)
    {
        $producto_existe = false;
        $producto_id = $id;
        foreach ($this->productos_pedido as $productos) {
            if ($productos['producto_id'] == $id) {
                $producto_existe = true;
                $producto_id = $productos['producto_id'];
            }
        }
        if ($producto_existe == true) {
            $producto = array_search($producto_id, array_column($this->productos_pedido, 'producto_id'));
            $this->productos_pedido[$producto]['cantidad'] = $this->productos_pedido[$producto]['cantidad'] + $this->unidades_producto;
        } else {
            $this->productos_pedido[] = ['producto_id' => $id, "cantidad" => $this->unidades_producto];
        }
        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->emit('refreshComponent');
    }

    public function submit()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'qr_id' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'almacen_id' => 'required',
                'observaciones' => 'nullable',
            ],
            // Mensajes de error
            [
                'qr_id.required' => 'El precio del pedido es obligatorio.',
                'lote_id.required' => 'El numero de orden es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        // Guardar datos validados
        $mercaderiaSave = Stock::create($validatedData);
        $dia = Carbon::now();
        foreach ($this->productos_pedido as $productosIndex => $productos) {
            $producto = Productos::find($productos['producto_id']);
            for ($i = 0; $i < $productos['cantidad']; $i++) {
                $lote_id = $dia->format('ymd') . $producto->id . $i;
                StockEntrante::create([
                    'producto_id' => $productos['producto_id'],
                    'lote_id' => $lote_id,
                    'stock_id' => $mercaderiaSave->id,
                    'cantidad' => $producto->cajas_por_pallet,
                ]);
            }
        }

        // Alertas de guardado exitoso
        if ($mercaderiaSave) {
            $this->alert('success', '¡Stock entrante registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la entrada del stock!', [
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
        $this->alert('warning', 'Comprueba que el stock introducido es correcto antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submit',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock.index');
    }
}
