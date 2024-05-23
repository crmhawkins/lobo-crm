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
use App\Models\User;
use App\Models\Delegacion;

use Illuminate\Support\Facades\DB;
//pdf
use Barryvdh\DomPDF\Facade\Pdf;
class IndexComponent extends Component
{
    public $pedidos;
    public $clientes;
    public $arrFiltrado = [];
    public $comerciales = [];
    public $delegaciones = [];
    public $delegacionSeleccionadaCOD = -1;
    public $comercialSeleccionadoId = -1;
    public $clienteSeleccionadoId = -1;
    public $estadoSeleccionado = -1;
    public $fecha_min = null;
    public $fecha_max = null;
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatePedidos()
    {
        $query = Pedido::query();
        
        $delegacion = Delegacion::where('COD', $this->delegacionSeleccionadaCOD)->first();
        
        if ($this->delegacionSeleccionadaCOD && $this->delegacionSeleccionadaCOD != -1) {
            $query->whereHas('cliente', function ($query) {
                $query->where('delegacion_COD', $this->delegacionSeleccionadaCOD);
            });
        }

        if($this->estadoSeleccionado && $this->estadoSeleccionado != -1){
            $estado = $this->getEstadoId($this->estadoSeleccionado);
            $query->where('estado',  $estado);
        }

        if($this->clienteSeleccionadoId && $this->clienteSeleccionadoId != -1){
            $query->where('cliente_id', $this->clienteSeleccionadoId);
        }

        if($this->comercialSeleccionadoId && $this->comercialSeleccionadoId != -1){
            $query->whereHas('cliente', function ($query) {
                $query->where('comercial_id', $this->comercialSeleccionadoId);
            });
        }

        //rango entre fecha min y fecha max
        if($this->fecha_min){
            $query->where('fecha', '>=', $this->fecha_min);
        }
        if($this->fecha_max){
            $query->where('fecha', '<=', $this->fecha_max);
        }


        
        $this->pedidos = $query->get();

        //dd($this->pedidos);

        $this->emit('refreshComponent');
    }
    public function limpiarFiltros()
    {
        $this->delegacionSeleccionadaCOD = -1;
        $this->comercialSeleccionadoId = -1;
        $this->estadoSeleccionado = -1;
        $this->clienteSeleccionadoId = -1;
        $this->fecha_min = null;
        $this->fecha_max = null;
        $this->updatePedidos();
    }
    public function updated($propertyName)
    {
        if (
            $propertyName == 'delegacionSeleccionadaCOD' ||
            $propertyName == 'comercialSeleccionadoId' ||
            $propertyName == 'estadoSeleccionado' ||
            $propertyName == 'clienteSeleccionadoId' ||
            $propertyName == 'fecha_min' ||
            $propertyName == 'fecha_max'
        ) {
            $this->updatePedidos();
        }
    }

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
        $this->comerciales = User::whereIn('role', [2, 3])->get();
        $this->delegaciones = Delegacion::all();
    }

    public function getComercial($id)
    {
        $comerciales = User::whereIn('role', [2, 3])->get();

        $cliente = $this->clientes->find($id);
        
        if (isset($cliente)) {
            $comercial = $comerciales->where('id', $cliente->comercial_id)->first();
            if (isset($comercial)) {
                return $comercial->name;
            } else {
                return "no definido";
            }
        } else {

            return "no definido";
        }
    }
    public function getDelegacion($id)
    {
        $delegaciones = Delegacion::all();
        $cliente = $this->clientes->find($id);
        if (isset($cliente)) {
            return $delegaciones->where('COD', $cliente->delegacion_COD)->first()->nombre;
        }
        return "no definido";
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

    public function getEstadoId($estado){
        return PedidosStatus::firstWhere('status', $estado)->id;
    }

    public function albaranExiste($pedidoId)
    {
        $albaran = Albaran::where('pedido_id', $pedidoId)->first();
        if ($albaran) {
            return true;
        }
        return false;
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
