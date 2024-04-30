<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Clients;
use App\Models\Pedido;
use App\Models\PedidosStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Albaran;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\StockEntrante;
use Illuminate\Support\Facades\DB;
//pdf
use Barryvdh\DomPDF\Facade\Pdf;
class IndexComponent extends Component
{
    public $pedidos;
    public $clientes;

    public function mount()
    {
        if(Auth::user()->role != 3){
            $this->pedidos = Pedido::all();
        }else{
            $this->pedidos = Clients::with('pedidos')->where('comercial_id', Auth::user()->id)
                        ->get()
                        ->pluck('pedidos')
                        ->flatten();
        }
        $this->clientes = Clients::all();
    }

    public function getClienteNombre($id){
        $cliente = $this->clientes->find($id);

        $nombre = $cliente->nombre;
        $apellido = $cliente->apellido;

        return "$nombre $apellido";
    }

    public function render()
    {
        return view('livewire.pedidos.index-component');
    }

    public function getEstadoNombre($estado){
        return PedidosStatus::firstWhere('id', $estado)->status;
    }


    public function albaran($pedidoId)
    {
        

        // Buscar el albarán asociado con el ID del pedido
		//$factura = Facturas::find($pedidoId);
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
            $stockEntrante = StockEntrante::where('id',$productoPedido->lote_id)->first();
            if (!isset( $stockEntrante)){
                $stockEntrante = StockEntrante::where('lote_id',$productoPedido->lote_id)->first();
            }
            if ($producto) {
                $productos[] = [
                    'nombre' => $producto->nombre,
                    'cantidad' => $productoPedido->unidades,
                    'precio_ud' => $productoPedido->precio_ud,
                    'precio_total' => $productoPedido->precio_total,
                    'iva' => $producto->iva,
                    'productos_caja' => isset($producto->unidades_por_caja) ? $producto->unidades_por_caja : null,
                    'lote_id' => isset($stockEntrante->orden_numero) ? $stockEntrante->orden_numero : '-----------' ,
                    'peso_kg' => ($producto->peso_neto_unidad * $productoPedido->unidades) / 1000,
                ];
            }
        }

        $datos = [
        'conIva' => false,
        'pedido' => $pedido ,
        'cliente' => $cliente,
        'productos' => $productos,
        'num_albaran' => $num_albaran = $albaran->num_albaran,
        'fecha_albaran' => $fecha_albaran = $albaran->fecha,
        ];

        // Generar y mostrar el PDF
        $pdf = PDF::loadView('livewire.almacen.pdf-component', $datos)->setPaper('a4', 'vertical');
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "albaran_{$albaran->num_albaran}.pdf"
        );
    }
}
