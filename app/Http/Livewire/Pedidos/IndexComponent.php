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
use App\Models\Configuracion;

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
        // Guardar los filtros en la sesión
        session([
            'pedido_filtro_delegacionSeleccionadaCOD' => $this->delegacionSeleccionadaCOD,
            'pedido_filtro_comercialSeleccionadoId' => $this->comercialSeleccionadoId,
            'pedido_filtro_clienteSeleccionadoId' => $this->clienteSeleccionadoId,
            'pedido_filtro_estadoSeleccionado' => $this->estadoSeleccionado,
            'pedido_filtro_fecha_min' => $this->fecha_min,
            'pedido_filtro_fecha_max' => $this->fecha_max,
        ]);

        $query = Pedido::query();
        
        $delegacion = Delegacion::where('COD', $this->delegacionSeleccionadaCOD)->first();
        if (Auth::user()->role == 3) {
            $query->whereHas('cliente', function ($query) {
                $query->where('comercial_id', Auth::user()->id);
            });
        }

        if ($this->delegacionSeleccionadaCOD && $this->delegacionSeleccionadaCOD != -1) {
            $query->whereHas('cliente', function ($query) {
                $query->where('delegacion_COD', $this->delegacionSeleccionadaCOD);
            });
        }

        if ($this->estadoSeleccionado && $this->estadoSeleccionado != -1) {
            $estado = $this->getEstadoId($this->estadoSeleccionado);
            $query->where('estado', $estado);
        }

        if ($this->clienteSeleccionadoId && $this->clienteSeleccionadoId != -1) {
            $query->where('cliente_id', $this->clienteSeleccionadoId);
        }

        if ($this->comercialSeleccionadoId && $this->comercialSeleccionadoId != -1) {
            $query->whereHas('cliente', function ($query) {
                $query->where('comercial_id', $this->comercialSeleccionadoId);
            });
        }

        // Rango entre fecha min y fecha max
        if ($this->fecha_min) {
            $query->where('fecha', '>=', $this->fecha_min);
        }
        if ($this->fecha_max) {
            $query->where('fecha', '<=', $this->fecha_max);
        }

        $this->pedidos = $query->get();

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

        // Limpiar filtros de la sesión
        session()->forget([
            'pedido_filtro_delegacionSeleccionadaCOD',
            'pedido_filtro_comercialSeleccionadoId',
            'pedido_filtro_clienteSeleccionadoId',
            'pedido_filtro_estadoSeleccionado',
            'pedido_filtro_fecha_min',
            'pedido_filtro_fecha_max'
        ]);

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
        // Recuperar filtros desde la sesión si existen
        $this->delegacionSeleccionadaCOD = session('pedido_filtro_delegacionSeleccionadaCOD', -1);
        $this->comercialSeleccionadoId = session('pedido_filtro_comercialSeleccionadoId', -1);
        $this->clienteSeleccionadoId = session('pedido_filtro_clienteSeleccionadoId', -1);
        $this->estadoSeleccionado = session('pedido_filtro_estadoSeleccionado', -1);
        $this->fecha_min = session('pedido_filtro_fecha_min', null);
        $this->fecha_max = session('pedido_filtro_fecha_max', null);

        if (Auth::user()->role != 3) {
            $this->pedidos = Pedido::all();
        } else {
            $this->pedidos = Clients::with('pedidos')->where('comercial_id', Auth::user()->id)
                ->get()
                ->pluck('pedidos')
                ->flatten();

            if (Auth::user()->user_department_id == 2) {
                $this->pedidos = Clients::with('pedidos')->where('comercial_id', Auth::user()->id)
                    ->where('delegacion_COD', 0)
                    ->orWhere('delegacion_COD', 16)
                    ->where('estado', 2)
                    ->get()
                    ->pluck('pedidos')
                    ->flatten();
            }
        }
        $this->clientes = Clients::all();
        $this->comerciales = User::whereIn('role', [2, 3])->get();
        $this->delegaciones = Delegacion::all();

        if (Auth::user()->role == 3) {
            $this->comercialSeleccionadoId = Auth::user()->id;
            $this->comerciales = User::where('id', Auth::user()->id)->get();
            $this->delegacionSeleccionadaCOD = Auth::user()->delegacion_COD;
            $this->delegaciones = Delegacion::where('COD', Auth::user()->delegacion_COD)->get();
            $this->clientes = Clients::where('comercial_id', Auth::user()->id)->get();
            if (Auth::user()->user_department_id == 2) {
                $this->clientes = Clients::where('comercial_id', Auth::user()->id)
                    ->orWhere('delegacion_COD', 0)
                    ->orWhere('delegacion_COD', 16)
                    ->where('estado', 2)
                    ->get();
                $this->delegaciones = Delegacion::where('COD', Auth::user()->delegacion_COD)
                    ->orWhere('COD', 0)
                    ->orWhere('COD', 16)
                    ->get();
            }
        }else{
            $this->updatePedidos();

        }
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
        $configuracion = Configuracion::where('id', 1)->first();
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
        'configuracion' => $configuracion,
        ];

        // Generar y mostrar el PDF
        $pdf = PDF::loadView('livewire.almacen.pdf-component', $datos)->setPaper('a4', 'vertical');
        $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });
        return response()->streamDownload(
            fn () => print($pdf->output()),
            "albaran_{$albaran->num_albaran}.pdf"
        );
    }
}
