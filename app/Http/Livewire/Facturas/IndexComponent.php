<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\Clients;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\StockEntrante;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;

class IndexComponent extends Component
{
    // public $search;
    public $pedidos;
    public $facturas;
    public $clientes;

    public function mount()
    {
        $this->pedidos = Pedido::all();
        $this->clientes = Clients::all();
        $this->facturas = Facturas::all();
    }

    public function render()
    {

        return view('livewire.facturas.index-component');
    }

    public function getCliente($id)
    {
        $cliente=$this->clientes->find($id);
        if(isset($cliente)){
        return $cliente->nombre;
        }
        return "Cliente no definido";
    }
    public function getListeners()
    {
        return [
            'pdf',
        ];
    }
    public function pdf($id,$iva)
    {

        $factura = Facturas::find($id);

        if($factura != null){
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran :: where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact= Productos::find($factura->producto_id);
            $productos = [];
            if(isset($pedido)){
            $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
            // Preparar los datos de los productos del pedido
            foreach ($productosPedido as $productoPedido) {
                $producto = Productos::find($productoPedido->producto_pedido_id);
                $stockEntrante = StockEntrante::where('id',$productoPedido->lote_id)->first();
                if (!isset( $stockEntrante)){
                    $stockEntrante = StockEntrante::where('lote_id',$productoPedido->lote_id)->first();
                }
                if ($stockEntrante){
                    $lote=$stockEntrante->orden_numero;
                }else{
                    $lote = "";
                }
                if ($producto) {
                    if(!isset($producto->peso_neto_unidad) || $producto->peso_neto_unidad <=0){
                        $peso ="Peso no definido";
                    }else{
                    $peso = ($producto->peso_neto_unidad * $productoPedido->unidades) /1000;
                    }
                    $productos[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $productoPedido->unidades,
                        'precio_ud' => $productoPedido->precio_ud,
                        'precio_total' => $productoPedido->precio_total,
                        'iva' => $producto->iva,
                        'lote_id' => $lote,
                        'peso_kg' =>  $peso,
                    ];
                }
            }}

            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'productos' => $productos,
                'producto' => $productofact,
            ];

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos)->setPaper('a4', 'vertical');;
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "factura_{$factura->id}.pdf");
        }else{
            return redirect('admin/facturas');
        }


    }


}
