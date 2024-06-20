<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\Clients;
use App\Models\Delegacion;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\StockEntrante;
use App\Models\Configuracion;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use App\Models\ServiciosFacturas;
use App\Mail\RecordatorioMail;
use Illuminate\Support\Facades\Mail;
use App\Models\RegistroEmail;
use Illuminate\Support\Facades\Log;
use App\Models\Emails;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexComponent extends Component
{

    use LivewireAlert;
    // public $search;
    public $pedidos;
    public $facturas;
    public $clientes;
    public $totalIva;
    public $totalesConIva;
    public $totalImportes;

    public $delegaciones;
    public $delegacionSeleccionadaCOD = -1;

    public $comerciales;
    public $comercialSeleccionadoId = -1;
    public $clienteSeleccionadoId = -1;

    public $arrFiltrado = [];
    public $arrDescargaFacturas = [];
    public $check;
    public $estadoSeleccionado = -1;
    public $tipoFactura = -1;
    public $fecha_min;
    public $fecha_max;
    public $emailsSeleccionados = [];

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
            $this->facturas = Facturas::where('tipo', 1)->orWhere('tipo', null)->get();
            //por cada factura se calcula el total de iva y el total de importes y el totales con iva
            $this->calcularTotales($this->facturas);
        }
    }

    public function getFacturaAsociada($id)
    {
        
        $factura = Facturas::where('id', $id)->first();

        if(!$factura){
            return 'No definido';
        }

        return $factura->factura_id;
    }

    public function updateFacturas()
    {
        $query = Facturas::query();

        if ($this->tipoFactura == -1) {
            $query->where('tipo', 1)->orWhere('tipo', null);
        }elseif($this->tipoFactura == 2){
            $query->where('tipo', 2);
        }elseif($this->tipoFactura == 3){
            $query->where('tipo', 3);
        }
        
        if ($this->delegacionSeleccionadaCOD && $this->delegacionSeleccionadaCOD != -1) {
            $query->whereHas('cliente', function ($query) {
                $query->where('delegacion_COD', $this->delegacionSeleccionadaCOD);
            });
        }

        if ($this->comercialSeleccionadoId && $this->comercialSeleccionadoId != -1) {
            $query->whereHas('cliente', function ($query) {
                $query->where('comercial_id', $this->comercialSeleccionadoId);
            });
        }

        if ($this->estadoSeleccionado && $this->estadoSeleccionado != -1) {
            switch ($this->estadoSeleccionado) {
                case 'vencidas':
                    $query->where(function ($query) {
                        $query->where('estado', 'Pendiente')
                            ->orWhere('estado', 'Cancelado');
                    })->whereDate('fecha_vencimiento', '<=', now());
                    break;
                case 'pagadas':
                    $query->where('estado', 'Pagado');
                    break;
                case 'pendientes':
                    $query->where('estado', 'Pendiente')
                        ->whereDate('fecha_vencimiento', '>', now());
                    break;
                default:
                    break;
            }
        }

        if ($this->clienteSeleccionadoId && $this->clienteSeleccionadoId != -1) {
            $query->where('cliente_id', $this->clienteSeleccionadoId);
        }

        //rango entre fecha min y fecha max
        if($this->fecha_min){
            $query->where('fecha_emision', '>=', $this->fecha_min);
        }
        if($this->fecha_max){
            $query->where('fecha_emision', '<=', $this->fecha_max);
        }

        
        $this->facturas = $query->get();
        $this->calcularTotales($this->facturas);

        //dd($this->pedidos);

        $this->emit('refreshComponent');
    }
    public function limpiarFiltros()
    {
        $this->delegacionSeleccionadaCOD = -1;
        $this->comercialSeleccionadoId = -1;
        $this->estadoSeleccionado = -1;
        $this->clienteSeleccionadoId = -1;
        $this->tipoFactura = -1;
        $this->fecha_min = null;
        $this->fecha_max = null;
        $this->updateFacturas();
    }
    public function updated($propertyName)
    {
        if (
            $propertyName == 'delegacionSeleccionadaCOD' ||
            $propertyName == 'comercialSeleccionadoId' ||
            $propertyName == 'estadoSeleccionado' ||
            $propertyName == 'clienteSeleccionadoId' ||
            $propertyName == 'tipoFactura' || 
            $propertyName == 'fecha_min' ||
            $propertyName == 'fecha_max'

        ) {
            $this->updateFacturas();
        }
    }

    

    public function descargarFacturas($array) {
        $this->arrDescargaFacturas = $array;
        $pdfs = [];

        // Itera sobre cada factura y genera un PDF por cada una
        foreach($this->arrDescargaFacturas as $index => $facturaId) {
            // Llama a la función descargarPdfs para generar el PDF de la factura
            $pdf = $this->descargarPdfs($facturaId);

            // Verifica si el PDF es válido antes de agregarlo a la matriz
            if ($pdf !== null) {
                // Agrega el PDF a la matriz
                $pdfs["factura_{$facturaId}.pdf"] = $pdf->output();
            } else {
                // Si el PDF es nulo, puedes registrar un mensaje de error o realizar alguna otra acción
                Log::error("Error al generar el PDF para la factura {$facturaId}");
            }
        }

        // Crea un archivo ZIP para contener todos los PDFs
        $zipFileName = 'facturas.zip';
        $zip = new ZipArchive;
        if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
            // Agrega cada PDF a la carpeta raíz del archivo ZIP
            foreach ($pdfs as $pdfName => $pdfContent) {
                $zip->addFromString($pdfName, $pdfContent);
            }
            $zip->close();
        } else {
            // Si no se puede crear el archivo ZIP, puedes registrar un mensaje de error o realizar alguna otra acción
            Log::error("Error al crear el archivo ZIP para las facturas");
        }

        // Descarga el archivo ZIP
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    


    public function updateFactura($facturasActualizadas)
    {
        $this->facturas = $facturasActualizadas;
        $this->calcularTotales($this->facturas);
       // $this->emit('actualizarTablaDespues');
    }

   

    public function calcularTotales($facturas)
    {
        $this->totalIva = 0;
        $this->totalImportes = 0;
        $this->totalesConIva = 0;
        foreach ($facturas as $factura) {
            $this->totalImportes += $factura->precio;
            $this->totalIva += $factura->iva;
            $this->totalesConIva += $factura->total;
            
        }

        $this->totalImportes = round($this->totalImportes, 2);
        $this->totalIva = round($this->totalIva, 2);
        $this->totalesConIva = round($this->totalesConIva, 2);
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
            'descargarFacturas',
            'enviarRecordatorio'
        ];
    }

   public function hasPedido($facturaId)
    {
        $factura = Facturas::find($facturaId);
        $pedido = Pedido::find($factura->pedido_id);
        return isset($pedido);
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

        $nota = null;
        if(isset($factura)){
            if(isset($factura->descripcion) && $factura->descripcion != null){
                $nota = $factura->descripcion;
            }
        }

        $datos = [
            'conIva' => false,
            'pedido' => $pedido,
            'cliente' => $cliente,
            'productos' => $productos,
            'num_albaran' => $num_albaran = $albaran->num_albaran,
            'fecha_albaran' => $fecha_albaran = $albaran->fecha,
            'nota' => $nota ?? null,
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
        $configuracion = Configuracion::first();
        if ($factura != null) {
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran::where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact = Productos::find($factura->producto_id);
            $productos = [];
            if($factura->tipo == 3){
                $servicios = ServiciosFacturas::where('factura_id', $factura->id)->get();
            }
            //dd($albaran);
           
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
            $productosFactura = DB::table('productos_factura')->where('factura_id', $factura->id)->get();
            $productosdeFactura = [];
            foreach ($productosFactura as $productoPedido) {
                $producto = Productos::find($productoPedido->producto_id);
                $stockEntrante = StockEntrante::where('id', $productoPedido->stock_entrante_id)->first();
               
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
                    $productosdeFactura[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $productoPedido->cantidad,
                        'precio_ud' => $productoPedido->precio_ud,
                        'precio_total' =>  ($productoPedido->cantidad * $productoPedido->precio_ud),
                        'iva' => $producto->iva != 0 ?  (($productoPedido->cantidad * $productoPedido->precio_ud) * $producto->iva / 100) : (($productoPedido->cantidad * $productoPedido->precio_ud) * 21 / 100) ,
                        'lote_id' => $lote,
                        'peso_kg' =>  $peso,
                    ];
                }
            }
            $total = 0;
            $base_imponible = 0;
            $iva_productos = 0;

            if ($factura->tipo == 2){
                
                foreach ($productosdeFactura as $producto) {
                    $base_imponible += $producto['precio_total'];
                    $iva_productos += $producto['iva'];
                }
                $total = $base_imponible + $iva_productos;

            }

            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'productos' => $productos,
                'producto' => $productofact,
                'configuracion' => $configuracion,
                'servicios' => $servicios ?? null,
                'productosFactura' => $productosdeFactura,
                'total' => $total,
                'base_imponible' => $base_imponible,
                'iva_productos' => $iva_productos,
                
            ];
            
            // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
            $pdf = Pdf::loadView('livewire.facturas.pdf-component', $datos)->setPaper('a4', 'vertical');

            return response()->streamDownload(
                fn () => print($pdf->output()),
                // "factura_{$factura->numero_factura}.pdf");
                "{$factura->numero_factura}.pdf"
            );


        } else {
            return redirect('admin/facturas');
        }
    }




    public function enviarRecordatorio($id){
        $factura = Facturas::find($id);
        $configuracion = Configuracion::first();
        if ($factura != null) {
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran::where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact = Productos::find($factura->producto_id);
            $productos = [];
            if($factura->tipo == 3){
                $servicios = ServiciosFacturas::where('factura_id', $factura->id)->get();
            }
            //dd($albaran);
           
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
            $productosFactura = DB::table('productos_factura')->where('factura_id', $factura->id)->get();
            $productosdeFactura = [];
            foreach ($productosFactura as $productoPedido) {
                $producto = Productos::find($productoPedido->producto_id);
                $stockEntrante = StockEntrante::where('id', $productoPedido->stock_entrante_id)->first();
               
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
                    $productosdeFactura[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $productoPedido->cantidad,
                        'precio_ud' => $productoPedido->precio_ud,
                        'precio_total' =>  ($productoPedido->cantidad * $productoPedido->precio_ud),
                        'iva' => $producto->iva != 0 ?  (($productoPedido->cantidad * $productoPedido->precio_ud) * $producto->iva / 100) : (($productoPedido->cantidad * $productoPedido->precio_ud) * 21 / 100) ,
                        'lote_id' => $lote,
                        'peso_kg' =>  $peso,
                    ];
                }
            }
            $total = 0;
            $base_imponible = 0;
            $iva_productos = 0;
            $iva = true;
            if ($factura->tipo == 2){
                
                foreach ($productosdeFactura as $producto) {
                    $base_imponible += $producto['precio_total'];
                    $iva_productos += $producto['iva'];
                }
                $total = $base_imponible + $iva_productos;

            }

            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'productos' => $productos,
                'producto' => $productofact,
                'configuracion' => $configuracion,
                'servicios' => $servicios ?? null,
                'productosFactura' => $productosdeFactura,
                'total' => $total,
                'base_imponible' => $base_imponible,
                'iva_productos' => $iva_productos,
                
            ];

                    
        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos)->setPaper('a4', 'vertical')->output();
        try{

            $emailsDireccion = [
                'Alejandro.martin@serlobo.com',
                'Ivan.ruiz@serlobo.com',
                'Administracion@serlobo.com',
                'Sandra.lopez@serlobo.com'
            ];

            $cliente = Clients::find($factura->cliente_id);
            if($cliente != null && $cliente->comercial_id != null){
                $comercial = User::find($cliente->comercial_id);
                if($comercial != null && $comercial->email != null){
                    $emailsDireccion[] = $comercial->email;
                }
            }

            $this->emailsSeleccionados = Emails::where('cliente_id', $cliente->id)->get();

            $this->emailsSeleccionados = $this->emailsSeleccionados->pluck('email')->toArray();
            //dd($this->emailsSeleccionados);

            if(count($this->emailsSeleccionados) > 0){
                

                Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new RecordatorioMail($pdf, $datos));

                foreach($this->emailsSeleccionados as $email){
                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $email;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->save();
                }

            }else{

                Mail::to($cliente->email)->bcc($emailsDireccion)->send(new RecordatorioMail($pdf, $datos));
                
                $registroEmail = new RegistroEmail();
                $registroEmail->factura_id = $factura->id;
                $registroEmail->pedido_id = null;
                $registroEmail->cliente_id = $factura->cliente_id;
                $registroEmail->email = $cliente->email;
                $registroEmail->user_id = Auth::user()->id;
                $registroEmail->save();

            }
            $this->alert('success', '¡Factura enviada por email correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);

        }catch(\Exception $e){
            //dd($e);
            $this->alert('error', '¡No se ha podido enviar la factura por email!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
        

        }
    }




    public function isPedidoMarketing($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);
        if ($pedido) {

            if($pedido->departamento_id == config('app.departamentos_pedidos')['Marketing']['id']){
                return true;
            }

        }
        return false;
    }
public function descargarPdfs($id)
    {

        $factura = Facturas::find($id);
        $iva = true;
        $configuracion = Configuracion::first();
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
                'configuracion' => $configuracion,
            ];
            
            // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
            $pdf = Pdf::loadView('livewire.facturas.pdf-component', $datos)->setPaper('a4', 'vertical');
            return $pdf;


        } 
    }


}
