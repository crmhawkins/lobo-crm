<?php

namespace App\Http\Livewire\Almacen;

use Illuminate\Support\Facades\Auth;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\StockEntrante;
use App\Models\Facturas;
use App\Models\Productos;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Albaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Alertas;


class IndexComponent extends Component
{
    // public $search;
    use LivewireAlert;
    public $pedidos_pendientes;
    public $pedidos_preparacion;
    public $pedidos_enviados;


    public function mount()
    {
        $userAlmacenId = Auth::user()->almacen_id; // Obtiene el almacen_id del usuario autenticado

        // Filtrar pedidos basados en almacen_id
        if ($userAlmacenId == 0) {
            // El usuario puede ver todos los pedidos
            $this->pedidos_pendientes = Pedido::where('estado', 2)->get();
            $this->pedidos_preparacion = Pedido::where('estado', 3)->get();
            $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->get();
        } else {
            // El usuario solo puede ver los pedidos de su almacén
            $this->pedidos_pendientes = Pedido::where('estado', 2)->where('almacen_id', $userAlmacenId)->get();
            $this->pedidos_preparacion = Pedido::where('estado', 3)->where('almacen_id', $userAlmacenId)->get();
            $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])
            ->where('almacen_id', $userAlmacenId)
            ->get();
        }
    }

    public function render()
    {

        return view('livewire.almacen.index-component');
    }

    public function getNombreCliente($id){
        return Clients::where('id', $id)->first()->nombre;
    }
    public function getListeners()
    {
        return [
            'prepararPedido',
        ];
    }
    public function prepararPedido($identificador)
    {
        $pedido = Pedido::find($identificador);
        $pedidosSave = $pedido->update(['estado' => 3]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Preparación',
                'descripcion' => 'El pedido nº ' . $pedido->id.' esta en preparación',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);
            $this->alert('success', '¡Pedido en preparación!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
            $userAlmacenId = Auth::user()->almacen_id; // Obtiene el almacen_id del usuario autenticado

            if ($userAlmacenId == 0) {
                // El usuario puede ver todos los pedidos
                $this->pedidos_pendientes = Pedido::where('estado', 2)->get();
                $this->pedidos_preparacion = Pedido::where('estado', 3)->get();
            } else {
                // El usuario solo puede ver los pedidos de su almacén
                $this->pedidos_pendientes = Pedido::where('estado', 2)->where('almacen_id', $userAlmacenId)->get();
                $this->pedidos_preparacion = Pedido::where('estado', 3)->where('almacen_id', $userAlmacenId)->get();
            }

        } else {
            $this->alert('error', '¡No se ha podido poner en preparación el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function enRuta($identificador)
    {
        $pedido = Pedido::find($identificador);
        $pedidosSave = $pedido->update(['estado' => 8]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: En Ruta ',
                'descripcion' => 'El pedido nº ' . $pedido->id . ' esta en ruta',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);
            $this->alert('success', '¡Pedido en Ruta!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);

            $userAlmacenId = Auth::user()->almacen_id; // Obtiene el almacen_id del usuario autenticado
            if ($userAlmacenId == 0) {
                // El usuario puede ver todos los pedidos
                $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->get();
            } else {
                // El usuario solo puede ver los pedidos de su almacén
                $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])
                ->where('almacen_id', $userAlmacenId)
                ->get();
            }

        } else {
            $this->alert('error', '¡No se ha podido poner en preparación el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function mostrarAlbaran($pedidoId,$Iva)
    {
        // Buscar el albarán asociado con el ID del pedido
        $albaran = Albaran::where('pedido_id', $pedidoId)->first();

        if (!$albaran) {
            $this->alert('error', 'Albarán no encontrado para el pedido especificado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        $pedido = Pedido::find($pedidoId);
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
                    'iva' => $producto->iva,
                    'lote_id' => $productoPedido->lote_id,
                    'peso_kg' => 1000 / $producto->peso_neto_unidad * $productoPedido->unidades,
                ];
            }
        }

        $datos = [
        'conIva' => $Iva,
        'pedido' => $pedido ,
        'cliente' => $cliente,
        'observaciones' => $pedido->observaciones,
        'productos' => $productos,
        'num_albaran' => $num_albaran =  $albaran->num_albaran,
        'fecha_albaran' => $fecha_albaran =  $albaran->fecha,
        ];

        // Generar y mostrar el PDF
        $pdf = PDF::loadView('livewire.almacen.pdf-component', $datos)->setPaper('a4', 'vertical');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "albaran_{$albaran->num_albaran}.pdf"
        );
    }
    public function comprobarStockPedido($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) {
            $this->alert('error', 'Pedido no encontrado.');
            return;
        }

        $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
        $almacenId = $pedido->almacen_id;
        $mensaje = "Comprobación de stock para el pedido: {$pedido->id}\n";

        foreach ($productosPedido as $productoPedido) {
            $producto = Productos::find($productoPedido->producto_pedido_id);
            $stockTotal = StockEntrante::whereHas('stock', function ($query) use ($almacenId) {
                                $query->where('almacen_id', $almacenId);
                            })
                            ->where('producto_id', $productoPedido->producto_pedido_id)
                            ->sum('cantidad');

            $mensaje .= "Producto: {$producto->nombre}, Requerido: {$productoPedido->unidades}, En Stock: {$stockTotal} - ";

            if ($stockTotal >= $productoPedido->unidades) {
                $mensaje .= "Stock suficiente.\n";
            } else {
                $mensaje .= "Stock insuficiente.\n";
            }
        }

        $this->alert('info', $mensaje, [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => '',
            'confirmButtonText' => 'Entendido',
        ]);
    }
}


