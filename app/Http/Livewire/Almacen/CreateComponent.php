<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Alumno;
use App\Models\Clients;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\ProductoLote;
use App\Models\Productos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $identificador;
    public $pedido;
    public $pedido_id;
    public $num_albaran;
    public $cliente;
    public $fecha;
    public $fecha_vencimiento;
    public $descripcion;
    public $productos;
    public $lotes;
    public $clientes;
    public $total_factura;
    public $productos_pedido = [];
    public $estado = "Pendiente";
    public $observaciones;
    public $descuento;
    public function mount()
    {

        $this->pedido = Pedido::find($this->identificador);
        $this->pedido_id = $this->pedido->id;
        $this->descuento = $this->pedido->descuento;
        $this->cliente = Clients::where('id', $this->pedido->cliente_id)->first();
        $this->productos = Productos::all();
        $this->lotes = ProductoLote::all();
        $this->clientes = Clients::all();
        $this->num_albaran = Albaran::count() + 1;
        $this->fecha = Carbon::now()->format('Y-m-d');
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->identificador)->get();
        foreach ($productos as $producto) {
            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_lote_id' => $producto->producto_lote_id,
                'unidades_old' => $producto->unidades,
                'precio_ud' => $producto->precio_ud,
                'precio_total' => $producto->precio_total,
                'unidades' => 0,
                'borrar' => 0,
            ];
        }
    }

    public function render()
    {
        return view('livewire.almacen.create-component');
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        $this->total_factura = $this->pedido->precio;
        // Validación de datos
        $validatedData = $this->validate(
            [
                'num_albaran' => 'required',
                'pedido_id' => 'required|numeric|min:1',
                'fecha' => 'required',
                'observaciones' => 'nullable',
                'total_factura' => '',
            ],
            // Mensajes de error
            [
                'num_albaran.required' => 'Indique un nº de factura.',
                'fecha.required' => 'Ingrese una fecha de emisión',
                'pedido_id.min' => 'Seleccione un pedido',
            ]
        );

        $pedido = Pedido::firstWhere('id', $this->pedido_id)->update(['estado' => 5]);

        // Guardar datos validados
        $facturasSave = Albaran::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));

        // Alertas de guardado exitoso
        if ($facturasSave) {
            $this->alert('success', 'Factura registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la factura!', [
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
            'destroy',
            'listarPedido',
            'GenerarAlbaran',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('facturas.index');
    }
    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }


    public function getNombreLoteTabla($id)
    {
        $lote = $this->lotes->where('id', $id)->first();

        // Verifica si se encontró un lote
        if ($lote) {
            return $lote->lote_id;
        } else {
            return 'Lote no encontrado';
        }
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


    public function GenerarAlbaran()
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
        $producto = Productos::find($productoPedido->producto_lote_id);
        if ($producto) {
            $productos[] = [
                'nombre' => $producto->nombre,
                'cantidad' => $productoPedido->unidades,
                'precio_ud' => $productoPedido->precio_ud,
                'precio_total' => $productoPedido->precio_total,
                'iva' => $producto->iva,
            ];
        }
    }

    $num_albaran = Albaran::count() + 1;
    $fecha_albaran = Carbon::now()->format('Y-m-d');

    $datos = [
        'pedido' => $pedido,
        'cliente' => $cliente,
        'observaciones' => $pedido->observaciones,
        'productos' => $productos,
        'num_albaran' => $num_albaran,
        'fecha_albaran' => $fecha_albaran
    ];

     // Crear una instancia del modelo Albaran
     $albaran = new Albaran();
     $albaran->pedido_id = $pedido->id;
     $albaran->num_albaran = $num_albaran;
     $albaran->fecha = $fecha_albaran;
     $albaran ->observaciones = $pedido->observaciones;
     $albaran ->total_factura = $pedido->precio;
     // ... otros campos del albarán ...
     $albaranSave = $albaran->save(); // Guardar el albarán en la base de datos
     $pedidosSave = $pedido->update(['estado' => 4]);
     if ($pedidosSave && $albaranSave) {
        $this->alert('success', '¡Albarán Generado!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
            'timerProgressBar' => true,
        ]);
    } else {
        $this->alert('error', '¡No se ha podido generar el albarán!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);
        return;
    }

    $pdf = PDF::loadView('livewire.almacen.pdf-component', $datos)->setPaper('a4', 'vertical')->output();
    return response()->streamDownload(
        fn () => print($pdf),
        "albaran_{$num_albaran}.pdf"
    );
}

}
