<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\Clients;
use App\Models\Delegacion;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\StockEntrante;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Auth;

class IndexComponent extends Component
{
    // public $search;
    public $pedidos;
    public $facturas;
    public $clientes;
    public $totalIva;
    public $totalesConIva;
    public $totalImportes;

    public $delegaciones;
    public $delegacionSeleccionadaCOD;

    public $comerciales;
    public $comercialSeleccionadoId;
    public $clienteSeleccionadoId;

    public $arrFiltrado = [];


    public function mount()
    {
        $user = Auth::user();
        $user_rol = $user->role;
        $this->pedidos = Pedido::all();
        $this->clientes = Clients::all();
        $this->delegaciones = Delegacion::all();
        $this->comerciales = User::whereIn('role', [2, 3])->get();

        if ($user_rol == 3) {
            //comercial
            $clientes_comercial = Clients::where('comercial_id', $user->id)->get();

            foreach ($clientes_comercial as $cliente) {
                $this->facturas = Facturas::where('cliente_id', $cliente->id)->get();
            }
        } else {
            $this->facturas = Facturas::all();
            //por cada factura se calcula el total de iva y el total de importes y el totales con iva
            $this->calcularTotales($this->facturas);
        }
    }

    public function filtrar($id, $filtro)
    {

        $filtros = [
            1 => 'comerciales',
            2 => 'delegaciones',
            3 => 'clientes',
        ];

        //si el filtro existe en el array de filtros, se actualiza con lo que se le pasa por parámetro
        if (array_key_exists($filtro, $this->arrFiltrado)) {
            $this->arrFiltrado[$filtro] = $id;
        } else {
            //si no existe, se añade al array de filtros
            $this->arrFiltrado[$filtro] = $id;
        }

        //se filtran las facturas
        $this->filtrarFacturas($this->arrFiltrado);
    }

    public function filtrarFacturas($filtros)
    {
        $facturas = Facturas::all();
        $facturasFiltradas = [];

        //debe comprobar que filtros hay activos y buscar las facturas que cumplan con esos filtros que traen los ids, por lo que deberia
        //saber si estan los 3 filtros, 2 o 1
        if (count($filtros) == 3) {
            //si hay 3 filtros activos
            foreach ($facturas as $factura) {
                //dd($factura);
                if (isset($filtros[1]) && isset($filtros[2]) && isset($filtros[3])) {
                    $cliente = Clients::find($factura->cliente_id);
                    if ($cliente->comercial_id == $filtros[1] && $cliente->delegacion_COD == $filtros[2] && $factura->cliente_id == $filtros[3]) {
                        $facturasFiltradas[] = $factura;
                    }
                }
            }
        } else if (count($filtros) == 2) {
            //si hay 2 filtros activos
            foreach ($facturas as $factura) {
                if (isset($filtros[3]) && isset($filtros[1])) {
                    $cliente = Clients::find($factura->cliente_id);
                    if ($cliente->comercial_id == $filtros[1] && $factura->cliente_id == $filtros[3]) {
                        $facturasFiltradas[] = $factura;
                    }
                } else if (isset($filtros[3]) && isset($filtros[2])) {
                    $cliente = Clients::find($factura->cliente_id);
                    if ($cliente->delegacion_COD == $filtros[2] && $factura->cliente_id == $filtros[3]) {
                        $facturasFiltradas[] = $factura;
                    }
                } else if (isset($filtros[1]) && isset($filtros[2])) {
                    //debe buscar clientes que tengan el cod de la delegacion y el id del comercial
                    $cliente = Clients::find($factura->cliente_id);
                    if ($cliente->delegacion_COD == $filtros[2] && $cliente->comercial_id == $filtros[1]) {
                        $facturasFiltradas[] = $factura;
                    }
                }
            }
        } else if (count($filtros) == 1) {
            //si hay 1 filtro activo
            foreach ($facturas as $factura) {
                if (isset($filtros[3])) {
                    if ($factura->cliente_id == $filtros[3]) {
                        $facturasFiltradas[] = $factura;
                    }
                } else if (isset($filtros[1])) {
                    //el comercial id es el id que tiene asociado el cliente. Por lo que hay que ver que cliente tiene la factura y ver el comercial que tiene ese cliente.
                    $cliente = Clients::find($factura->cliente_id);
                    if ($cliente->comercial_id == $filtros[1]) {
                        $facturasFiltradas[] = $factura;
                    }
                } else if (isset($filtros[2])) {
                    //debe hacer lo mismo que con el comercial, ver que cliente tiene la factura y ver la delegacion que tiene ese cliente
                    $cliente = Clients::find($factura->cliente_id);
                    //dd($cliente->delegacion_COD, $filtros[2]);
                    if ($cliente->delegacion_COD == $filtros[2]) {
                        $facturasFiltradas[] = $factura;
                    }
                }
            }
        } else {
            //si no hay filtros activos
            $facturasFiltradas = $facturas;
        }

        $this->updateFactura($facturasFiltradas);
    }

    public function updating($property, $value)
    {

        if ($property == 'delegacionSeleccionadaCOD' || $property == 'comercialSeleccionadoId' || $property == 'clienteSeleccionadoId') {
            $this->emit('actualizarTablaAntes');
        }
    }



    public function updateFactura($facturasActualizadas)
    {
        $this->facturas = $facturasActualizadas;
        $this->calcularTotales($this->facturas);
        $this->emit('actualizarTablaDespues');
    }

    public function eliminarFiltro($filtro)
    {
        //se elimina el filtro del array de filtros
        unset($this->arrFiltrado[$filtro]);

        //se filtran las facturas
        //$this->emit('actualizarTablaAntes');
        $this->filtrarFacturas($this->arrFiltrado);
        //$this->emit('actualizarTablaDespues');
    }

    public function limpiarFiltros()
    {
        $this->arrFiltrado = [];

        //hay que hacerlo de forma asíncrona

        //se filtran las facturas


        //$this->emit('actualizarTablaAntes');
        $this->facturas = Facturas::all();
        $this->calcularTotales($this->facturas);
        //$this->emit('actualizarTablaDespues');
        $this->delegacionSeleccionadaCOD = null;
        $this->comercialSeleccionadoId = null;
        $this->clienteSeleccionadoId = null;
        $this->emit('actualizarTablaDespues');
        //dd($this->facturas);

    }

    public function onChangeFiltrado($filtro)
    {
        //dd($filtro);
        if ($filtro == 2 && $this->delegacionSeleccionadaCOD != null) {
            if ($this->delegacionSeleccionadaCOD == -1) {
                $this->eliminarFiltro(2);
            } else {
                $this->filtrar($this->delegacionSeleccionadaCOD, $filtro);
            }
        }

        if ($filtro == 1 && $this->comercialSeleccionadoId != null) {
            if ($this->comercialSeleccionadoId == -1) {
                $this->eliminarFiltro(1);
            } else {
                $this->filtrar($this->comercialSeleccionadoId, $filtro);
            }
        }

        if ($filtro == 3 && $this->clienteSeleccionadoId != null) {
            if ($this->clienteSeleccionadoId == -1) {
                $this->eliminarFiltro(3);
            } else {
                $this->filtrar($this->clienteSeleccionadoId, $filtro);
            }
        }
    }

    public function calcularTotales($facturas)
    {
        $this->totalIva = 0;
        $this->totalImportes = 0;
        $this->totalesConIva = 0;
        foreach ($facturas as $factura) {
            if (isset($factura->descuento)) {
                $importe = $factura->precio * (1 + (- ($factura->descuento) / 100));
                $iva = ($factura->precio * (1 + (- ($factura->descuento) / 100))) * 0.21;
                $totalesIva = ($factura->precio * (1 + (- ($factura->descuento) / 100))) * 1.21;
                //$this->totalImportes += number_format( $factura->precio * (1 + (-($factura->descuento) /100)),2);
                $this->totalImportes += $importe;
                $this->totalIva += $iva;
                $this->totalesConIva += $totalesIva;
                //dd($this->totalImportes);
            } else {
                $importe = $factura->precio;
                $iva = $factura->precio * 0.21;
                $totalesIva = $factura->precio * 1.21;
                $this->totalImportes += $importe;
                $this->totalIva += $iva;
                $this->totalesConIva += $totalesIva;
            }

            $this->totalImportes = round($this->totalImportes, 2);
            $this->totalIva = round($this->totalIva, 2);
            $this->totalesConIva = round($this->totalesConIva, 2);
        }
    }



    public function render()
    {
        return view('livewire.facturas.index-component');
    }

    public function getCliente($id)
    {
        $cliente = $this->clientes->find($id);
        if (isset($cliente)) {
            $clienteModel = Clients::find($id);
            $cliente['delegacion'] = isset($clienteModel->delegacion) ? $clienteModel->delegacion->nombre : 'No definido';
            $cliente['comercial'] = isset($clienteModel->comercial) ? $clienteModel->comercial->name : 'No definido';
            return $cliente;
        }
        return "Cliente no definido";
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
    public function getListeners()
    {
        return [
            'pdf',
            'albaran',
            'actualizarTabla',
            'limpiarFiltros',
        ];
    }
    public function albaran($pedidoId, $iva)
    {
        // Buscar el albarán asociado con el ID del pedido
        $factura = Facturas::find($pedidoId);
        $albaran = Albaran::where('pedido_id', $factura->pedido_id)->first();

        if (!$albaran) {
            $this->alert('error', 'Albarán no encontrado para el pedido especificado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        $pedido = Pedido::find($factura->pedido_id);
        if (!$pedido) {
            abort(404, 'Pedido no encontrado');
        }

        $cliente = Clients::find($pedido->cliente_id);
        $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

        // Preparar los datos de los productos del pedido
        $productos = [];
        foreach ($productosPedido as $productoPedido) {
            $producto = Productos::find($productoPedido->producto_pedido_id);
            $stockEntrante = StockEntrante::where('id', $productoPedido->lote_id)->first();
            if (!isset($stockEntrante)) {
                $stockEntrante = StockEntrante::where('lote_id', $productoPedido->lote_id)->first();
            }
            if ($producto) {
                $productos[] = [
                    'nombre' => $producto->nombre,
                    'cantidad' => $productoPedido->unidades,
                    'precio_ud' => $productoPedido->precio_ud,
                    'precio_total' => $productoPedido->precio_total,
                    'iva' => $producto->iva,
                    'productos_caja' => isset($producto->unidades_por_caja) ? $producto->unidades_por_caja : null,
                    'lote_id' => isset($stockEntrante->orden_numero) ? $stockEntrante->orden_numero : '-----------',
                    'peso_kg' => ($producto->peso_neto_unidad * $productoPedido->unidades) / 1000,
                ];
            }
        }

        $datos = [
            'conIva' => false,
            'pedido' => $pedido,
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
    public function pdf($id, $iva)
    {

        $factura = Facturas::find($id);

        if ($factura != null) {
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran::where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact = Productos::find($factura->producto_id);
            $productos = [];
            if (isset($pedido)) {
                $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
                // Preparar los datos de los productos del pedido
                foreach ($productosPedido as $productoPedido) {
                    $producto = Productos::find($productoPedido->producto_pedido_id);
                    $stockEntrante = StockEntrante::where('id', $productoPedido->lote_id)->first();
                    if (!isset($stockEntrante)) {
                        $stockEntrante = StockEntrante::where('lote_id', $productoPedido->lote_id)->first();
                    }
                    if ($stockEntrante) {
                        $lote = $stockEntrante->orden_numero;
                    } else {
                        $lote = "";
                    }
                    if ($producto) {
                        if (!isset($producto->peso_neto_unidad) || $producto->peso_neto_unidad <= 0) {
                            $peso = "Peso no definido";
                        } else {
                            $peso = ($producto->peso_neto_unidad * $productoPedido->unidades) / 1000;
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
                }
            }

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
            $pdf = Pdf::loadView('livewire.facturas.pdf-component', $datos)->setPaper('a4', 'vertical');;
            return response()->streamDownload(
                fn () => print($pdf->output()),
                // "factura_{$factura->numero_factura}.pdf");
                "{$factura->numero_factura}.pdf"
            );
        } else {
            return redirect('admin/facturas');
        }
    }
}
