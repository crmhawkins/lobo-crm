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
use App\Models\Alertas;

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
    public $stock;
    public $orden_numero;
    public $unidades_caja_producto;
    public $unidades_pallet_producto;

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
    public function deleteArticulo($id)
    {
        unset($this->productos_pedido[$id]);
        $this->productos_pedido = array_values($this->productos_pedido);
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

    public function addProducto($id)
    {
        $this->productos_pedido = [];
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
//     public function addProducto($id)
// {
//     // Limpiar cualquier producto existente
//     $this->productos_pedido = [];

//     // Añadir el producto seleccionado con una cantidad fija de 1 palet
//     $this->productos_pedido[] = ['producto_id' => $id, 'cantidad' => ""];

//     // Restablecer selecciones
//     $this->producto_seleccionado = 0;
//     $this->unidades_producto = 0;

//     // Refrescar el componente
//     $this->emit('refreshComponent');
// }

    public function submit()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'qr_id' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'almacen_id' => 'required',
                'orden_numero' => 'required',
                'observaciones' => 'nullable',
            ],
            // Mensajes de error
            [
                'qr_id.required' => 'El precio del pedido es obligatorio.',
                'lote_id.required' => 'El lote es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );


        $mercaderiaSave = Stock::create($validatedData);
        $dia = Carbon::now();
        foreach ($this->productos_pedido as $productosIndex => $productos) {
            $producto = Productos::find($productos['producto_id']);
            for ($i = 0; $i < $productos['cantidad']; $i++) {
                $lote_id = $dia->format('ymdHis') . $producto->id . $i;
                StockEntrante::create([
                    'producto_id' => $productos['producto_id'],
                    'lote_id' => $lote_id,
                    'stock_id' => $mercaderiaSave->id,
                    'cantidad' => $producto->cajas_por_pallet,
                    'orden_numero' => $validatedData['orden_numero'],
                ]);
            }
        }

        // Alertas de guardado exitoso
        if ($mercaderiaSave) {

            foreach ($this->productos_pedido as $productosIndex => $productos) {

                $producto = Productos::find($productos['producto_id']);
                $stockSeguridad =  $producto->stock_seguridad;
                $almacen = Almacen::find($this->almacen_id);

                $entradasAlmacen = Stock::where('almacen_id', $almacen->id)->get()->pluck('id');
                $productoLotes = StockEntrante::where('producto_id', $producto->id)->whereIn('stock_id', $entradasAlmacen)->get();
                $sumatorioCantidad = $productoLotes->sum('cantidad');

                if ($sumatorioCantidad > $stockSeguridad) {

                    $alertaExistente = Alertas::where('referencia_id', $producto->id.$almacen->id )->where('stage', 7)->first();
                    if(isset( $alertaExistente)){
                    $alertaExistente->delete();
                    }
                }
            }

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
