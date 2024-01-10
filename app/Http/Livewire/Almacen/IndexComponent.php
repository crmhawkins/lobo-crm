<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Facturas;
use App\Models\Productos;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Albaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class IndexComponent extends Component
{
    // public $search;
    use LivewireAlert;
    public $pedidos_pendientes;
    public $pedidos_preparacion;
    public $pedidos_enviados;


    public function mount()
    {
        $this->pedidos_pendientes = Pedido::where('estado', 2)->get();
        $this->pedidos_preparacion = Pedido::where('estado', 3)->get();
        $this->pedidos_enviados = Pedido::where('estado', 4)->get();

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
            $this->alert('success', '¡Pedido en preparación!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);

            $this->pedidos_pendientes = Pedido::where('estado', 2)->get();
            $this->pedidos_preparacion = Pedido::where('estado', 3)->get();

        } else {
            $this->alert('error', '¡No se ha podido poner en preparación el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function mostrarAlbaran($pedidoId)
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

        $datos = [
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
}


