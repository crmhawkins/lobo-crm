<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Almacen;
use App\Models\Clients;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\Stock;
use App\Models\StockEntrante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Alertas;

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
    public $almacen_id;
    public function mount()
    {
        $this->almacen_id = auth()->user()->almacen_id;
        $this->pedido = Pedido::find($this->identificador);
        $this->pedido_id = $this->pedido->id;
        $this->descuento = $this->pedido->descuento;
        $this->cliente = Clients::where('id', $this->pedido->cliente_id)->first();
        $this->productos = Productos::all();
        $this->lotes = StockEntrante::all();
        $this->clientes = Clients::all();
        $this->num_albaran = Albaran::count() + 1;
        $this->fecha = Carbon::now()->format('Y-m-d');
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
                'lote_id' => $producto->lote_id,
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
            'qrScanned' => 'handleQrScanned',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('almacen.index');
    }
    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }


    public function getPesoTotal($id,$In)
    {
        $pesoUnidad = $this->productos->where('id', $id)->first()->peso_neto_unidad;
        $Cantidad = $this->productos_pedido[$In]['unidades_old'];
        $pesoTotal= ($pesoUnidad * $Cantidad)/1000;
        return $pesoTotal;

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


    public function GenerarAlbaran()
    {
    $pedido = Pedido::find($this->identificador);
    if (!$pedido) {
        abort(404, 'Pedido no encontrado');
    }

        // Verificar que todos los productos tienen un lote_id asignado
    foreach ($this->productos_pedido as $productoPedido) {
        if (empty($productoPedido['lote_id'])) {
            $this->alert('error', 'Todos los productos deben tener un lote asignado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return;
        }else{
            DB::table('productos_pedido')
            ->where('pedido_id',$this->identificador)
            ->where('producto_pedido_id',$productoPedido['producto_pedido_id'])
            ->update(['lote_id' => $productoPedido['lote_id']
            ]);
        }
    }

    $cliente = Clients::find($pedido->cliente_id);
    $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

    // Preparar los datos de los productos del pedido
    $productos = [];
    foreach ($productosPedido as $productoPedido) {
        $producto = Productos::find($productoPedido->producto_pedido_id);
        $stockSeguridad =  $producto->stock_seguridad;
        $stockEntrante = StockEntrante::where('id',$productoPedido->lote_id)->first();
        $almacen_id = Stock::find($stockEntrante->stock_id)->almacen_id;
        $almacen = Almacen::find($almacen_id);

        if ($stockEntrante) {
            $stockEntrante->cantidad -= $productoPedido->unidades;
            $stockEntrante->update();
        }
        $entradasAlmacen = Stock::where('almacen_id', $almacen->id)->get()->pluck('id');
        $productoLotes = StockEntrante::where('producto_id', $producto->id)->whereIn('stock_id', $entradasAlmacen)->get();
        $sumatorioCantidad = $productoLotes->sum('cantidad');

        if ($sumatorioCantidad < $stockSeguridad) {
            $alertaExistente = Alertas::where('referencia_id', $producto->id . $almacen->id )->where('stage', 7)->first();
            if (!$alertaExistente) {
                Alertas::create([
                    'user_id' => 13,
                    'stage' => 7,
                    'titulo' => $producto->nombre.' - Alerta de Stock Bajo',
                    'descripcion' =>'Stock de '.$producto->nombre. ' insuficiente en el almacen de ' . $almacen->almacen,
                    'referencia_id' =>$producto->id . $almacen->id ,
                    'leida' => null,
                ]);
            }

         }
        if ($producto) {
            $productos[] = [
                'nombre' => $producto->nombre,
                'cantidad' => $productoPedido->unidades,
                'precio_ud' => $productoPedido->precio_ud,
                'precio_total' => $productoPedido->precio_total,
                'iva' => $producto->iva,
                'lote_id' => $stockEntrante->orden_numero,
                'peso_kg' => ($producto->peso_neto_unidad * $productoPedido->unidades) /1000 ,
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
        Alertas::create([
            'user_id' => 13,
            'stage' => 3,
            'titulo' => 'Estado del Pedido: Albarán',
            'descripcion' => 'Generado Albarán del pedido nº ' . $pedido->id,
            'referencia_id' => $pedido->id,
            'leida' => null,
        ]);
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

        public function handleQrScanned($qrCode, $rowIndex)
        {
            // Buscar en Stock con qrCode
            $stock = Stock::where('qr_id', $qrCode)->first();
                if (!$stock) {
                // Alerta de error si no se encuentra el stock
                    $this->alert('error', 'QR no asignado o inválido.', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'confirmButtonText' => 'Aceptar',
                    ]);
                    return;
                }

            // Buscar en StockEntrante con el stock_id obtenido
            $entradaStock = StockEntrante::where('stock_id', $stock->id)->first();

            if ($entradaStock && isset($this->productos_pedido[$rowIndex])) {
                // Comprobar si el producto_id de StockEntrante coincide con producto_pedido_id de productos_pedido
                if ($this->productos_pedido[$rowIndex]['producto_pedido_id'] == $entradaStock->producto_id ) {
                    if ($this->productos_pedido[$rowIndex]['unidades'] <= $entradaStock->cantidad) {
                        // Actualizar el lote_id en productos_pedido
                        $this->productos_pedido[$rowIndex]['lote_id'] = $entradaStock->id;
                    }else{
                        $this->alert('error', 'Lote con stock insuficiente', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'confirmButtonText' => 'Aceptar',
                    ]);}
                }else{
                    // Alerta de error si se intenta leer el QR de un producto diferente
                     $this->alert('error', 'Intentando leer el QR de un producto diferente.', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'confirmButtonText' => 'Aceptar',
                    ]);
                }
            }
        }
}
