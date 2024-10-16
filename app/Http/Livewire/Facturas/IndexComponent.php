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
use Dompdf\Dompdf;
use Dompdf\Options;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use App\Models\ServiciosFacturas;
use App\Mail\RecordatorioMail;
use Illuminate\Support\Facades\Mail;
use App\Models\RegistroEmail;
use Illuminate\Support\Facades\Log;
use App\Models\Emails;
use App\Models\Caja;
use App\Models\ProductosMarketingPedido;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\ProductosFacturas;

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
    
        // Recuperar filtros desde la sesión si existen
        $this->tipoFactura = session('factura_filtro_tipoFactura', -1);
        $this->delegacionSeleccionadaCOD = session('factura_filtro_delegacionSeleccionadaCOD', -1);
        $this->comercialSeleccionadoId = session('factura_filtro_comercialSeleccionadoId', -1);
        $this->estadoSeleccionado = session('factura_filtro_estadoSeleccionado', -1);
        $this->clienteSeleccionadoId = session('factura_filtro_clienteSeleccionadoId', -1);
        $this->fecha_min = session('factura_filtro_fecha_min', null);
        $this->fecha_max = session('factura_filtro_fecha_max', null);
    
        $this->pedidos = Pedido::all();
        $this->clientes = Clients::all();
        $this->delegaciones = Delegacion::all();
        $this->comerciales = User::whereIn('role', [2, 3])->get();
    
        if ($user_rol == 3) {
            // Comercial
            $clientes_comercial = Clients::where('comercial_id', $user->id)->get();
    
            foreach ($clientes_comercial as $cliente) {
                $this->facturas = Facturas::where('cliente_id', $cliente->id)->get();
            }
    
        } else {
            $this->updateFacturas();
        }
    }


    public function getTotalSobrante($facturaId)
{
    // Busca la factura
    $factura = Facturas::find($facturaId);
    $delegacion = $this->getDelegacion($factura->cliente_id);

    // Asegúrate de que la factura exista
    if (!$factura) {
        return "Factura no encontrada";
    }

    // Total de la factura en formato numérico (no formatees todavía)
    $totalFactura = round($factura->total, 2);
    if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA'){
        $totalFactura = round($factura->precio, 2);
    }

    if($factura->factura_rectificativa_id != null){
        $facturaRectificativa = Facturas::find($factura->factura_rectificativa_id);
        if(!$facturaRectificativa || $facturaRectificativa->total == null){
            $totalFactura = round($factura->total, 2);
            if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA'){
                $totalFactura = round($factura->precio, 2);
            }
        }else{
            $totalFactura = round($facturaRectificativa->total, 2);
            if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA'){
                $totalFactura = round($facturaRectificativa->precio, 2);
            }

        }
    }

   

    // Suma los ingresos en la caja
    $IngresosCaja = Caja::where('pedido_id', $factura->id)->sum('importe');

    // Calcula el sobrante de manera numérica
    $totalSobrante =  $totalFactura - $IngresosCaja ;

    if($totalSobrante < 0){
        $totalSobrante = 0;
    }

    // Si necesitas formatear para la visualización, hazlo después
    return number_format($totalSobrante, 2, ',', '.');
}


public function getImporte($facturaId){

    $factura = Facturas::find($facturaId);

    if(!$factura){
        return 'No definido';
    }

    //si la factura tiene rectificativa

    if($factura->factura_rectificativa_id != null){
        $facturaRectificativa = Facturas::find($factura->factura_rectificativa_id);
        if(!$facturaRectificativa || $facturaRectificativa->total == null){
            //numbert_format($factura->total, 2, ',', '.');

            return number_format($factura->precio, 2, '.', '');
        }else{
            
            return number_format($facturaRectificativa->precio, 2, '.', '');
        }
    }else{
        return number_format($factura->precio, 2, '.', '');
    }

}

public function getIva($facturaId){
    $factura = Facturas::find($facturaId);

    if(!$factura){
        return 'No definido';
    }
    $delegacion = $this->getDelegacion($factura->cliente_id);

    //si la factura tiene rectificativa
    if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA'){
        return number_format(0,2, '.', '');
    }
    if($factura->factura_rectificativa_id != null){
        $facturaRectificativa = Facturas::find($factura->factura_rectificativa_id);
        if(!$facturaRectificativa || $facturaRectificativa->total == null){
            //numbert_format($factura->total, 2, ',', '.');
            //hay que tener en cuenta si la delegacion es de las exentas de iva

            
            
            return number_format($factura->iva ?? $factura->precio * 0.21, 2, '.', '');
        }else{
            
            
            return number_format($facturaRectificativa->iva ?? $facturaRectificativa->precio * 0.21, 2, '.', '');
        }
    }else{
        
        return number_format($factura->iva ?? $factura->precio * 0.21, 2, '.', '');
    }

}
    

    public function getTotal($facturaId){

        $factura = Facturas::find($facturaId);

        if(!$factura){
            return 'No definido';
        }

        $delegacion = $this->getDelegacion($factura->cliente_id);

        
        if($factura->factura_rectificativa_id != null){
            $facturaRectificativa = Facturas::find($factura->factura_rectificativa_id);
            if(!$facturaRectificativa || $facturaRectificativa->total == null){
                //numbert_format($factura->total, 2, ',', '.');
                //si la factura tiene rectificativa
                if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA'){
                    return number_format($factura->precio, 2, '.', '');
                }
                return number_format($factura->total, 2, '.', '');
            }else{
                //si la factura tiene rectificativa
                if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA'){
                    return number_format($facturaRectificativa->precio, 2, '.', '');
                }
                return number_format($facturaRectificativa->total, 2, '.', '');
            }
        }else{
            if($factura->total != null){
                return number_format($factura->precio * 1.21, 2, '.', '');
            }
            return number_format($factura->total, 2, '.', '');
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
    public function getNumberFacturaAsociada($id)
    {
        
        $factura = Facturas::where('id', $id)->first();

        $facturaNormal = Facturas::where('id', $factura->factura_id)->first();

        if(!$factura){
            return 'No definido';
        }

        return $facturaNormal->numero_factura;
    }

    public function updateFacturas()
    {
        // Guardar los filtros en la sesión
        session([
            'factura_filtro_tipoFactura' => $this->tipoFactura,
            'factura_filtro_delegacionSeleccionadaCOD' => $this->delegacionSeleccionadaCOD,
            'factura_filtro_comercialSeleccionadoId' => $this->comercialSeleccionadoId,
            'factura_filtro_estadoSeleccionado' => $this->estadoSeleccionado,
            'factura_filtro_clienteSeleccionadoId' => $this->clienteSeleccionadoId,
            'factura_filtro_fecha_min' => $this->fecha_min,
            'factura_filtro_fecha_max' => $this->fecha_max,
        ]);

        $query = Facturas::query();

        // Los del rol 3 solo pueden ver sus facturas
        $user = Auth::user();
        $user_rol = $user->role;

        if ($this->tipoFactura == -1) {
            $query->where('tipo', 1)->orWhere('tipo', null);
        } elseif ($this->tipoFactura == 2) {
            $query->where('tipo', 2);
        } elseif ($this->tipoFactura == 3) {
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
                case 'Parcial':
                    $query->where('estado', 'Parcial');
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

        // Rango entre fecha min y fecha max
        if ($this->fecha_min) {
            $query->where('fecha_emision', '>=', $this->fecha_min);
        }
        if ($this->fecha_max) {
            $query->where('fecha_emision', '<=', $this->fecha_max);
        }

        if ($user_rol == 3) {
            // Comercial
            $clientes_comercial = Clients::where('comercial_id', $user->id)->get();
            $query->where(function ($query) use ($clientes_comercial) {
                foreach ($clientes_comercial as $cliente) {
                    $query->orWhere('cliente_id', $cliente->id);
                }
            });
        }

        $this->facturas = $query->get();
        $this->calcularTotales($this->facturas);

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

    // Limpiar filtros de la sesión
    session()->forget([
        'factura_filtro_tipoFactura',
        'factura_filtro_delegacionSeleccionadaCOD',
        'factura_filtro_comercialSeleccionadoId',
        'factura_filtro_estadoSeleccionado',
        'factura_filtro_clienteSeleccionadoId',
        'factura_filtro_fecha_min',
        'factura_filtro_fecha_max'
    ]);

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

            $factura = Facturas::find($facturaId);


            // Verifica si el PDF es válido antes de agregarlo a la matriz
            if ($pdf !== null) {
                // Agrega el PDF a la matriz
                $pdfs["{$factura->numero_factura}.pdf"] = $pdf->output();
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
        $delegacion = $this->getDelegacion($factura->cliente_id);

        // Sumar importe total
        $this->totalImportes += $factura->precio;

        // Sumar IVA si la delegación no está en la lista de exención
        if (!in_array($delegacion, ['07 CANARIAS', '13 GIBRALTAR', '14 CEUTA', '15 MELILLA'])) {
            $this->totalIva += $factura->iva;
        }

        // Sumar totales con IVA, considerando las delegaciones
        if (in_array($delegacion, ['07 CANARIAS', '13 GIBRALTAR', '14 CEUTA', '15 MELILLA'])) {
            $this->totalesConIva += $factura->precio; // Sin IVA
        } else {
            $this->totalesConIva += $factura->total; // Con IVA
        }
    }

    // Redondear los totales
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

        // Truncamos el nombre del cliente si es demasiado largo
        $cliente['nombre'] = $cliente['nombre']; // Puedes ajustar el límite de caracteres

        return $cliente;
    }
    return "Cliente no definido";
}

    // Función para truncar texto con puntos suspensivos
    private function truncarTexto($texto, $limite = 10)
    {
        if (strlen($texto) > $limite) {
            return substr($texto, 0, $limite) . '...';
        }
        return $texto;
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
            'enviarRecordatorio',
            'pdfRectificada',
            'pdfIntermedio' => 'pdfIntermedio',
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

        $productosMarketing = ProductosMarketingPedido::where('pedido_id', $pedido->id)->get();
        // dd($productosMarketing);


        $configuracion = Configuracion::where('id', 1)->first();
        $datos = [
            'conIva' => false,
            'pedido' => $pedido,
            'cliente' => $cliente,
            'productos' => $productos,
            'num_albaran' => $num_albaran = $albaran->num_albaran,
            'fecha_albaran' => $fecha_albaran = $albaran->fecha,
            'nota' => $nota ?? null,
            'configuracion' => $configuracion,
            'productosMarketing' => $productosMarketing
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
    public function pdf($id, $iva)
    {

        $factura = Facturas::find($id);
        $configuracion = Configuracion::first();
        if ($factura != null) {
            $pedido = Pedido::find($factura->pedido_id);

            $productosMarketing = ProductosMarketingPedido::where('pedido_id', $pedido->id)->get();

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
                'productosMarketing' => $productosMarketing,
                
            ];
            $pdf = Pdf::loadView('livewire.facturas.pdf-component', $datos)->setPaper('a4', 'vertical');
          

            $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });

            // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
            // $pdf = Pdf::loadView('livewire.facturas.pdf-component', $datos)->setPaper('a4', 'vertical');

            return response()->streamDownload(
                fn () => print($pdf->output()),
                // "factura_{$factura->numero_factura}.pdf");
                "{$factura->numero_factura}.pdf"
            );


        } else {
            return redirect('admin/facturas');
        }
    }

    public function pdfRectificada($id, $iva){

        $factura = Facturas::find($id);
        $configuracion = Configuracion::first();

        $facturasRectificativas = Facturas::where('factura_id', $id)->get();


        if ($factura != null) {
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran::where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact = Productos::find($factura->producto_id);
            $productos = [];
           
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
                            'id' => $producto->id,
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
            $arrProductosFactura = [];
            
            foreach ($facturasRectificativas as $facturaRectificativa){
                $productosdeFactura = [];
                $productosFactura = DB::table('productos_factura')->where('factura_id', $facturaRectificativa->id)->get();

                foreach($productosFactura as $productoPedido){
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
                        $arrProductosFactura[] = [
                            'id' => $producto->id,
                            'nombre' => $producto->nombre,
                            'cantidad' => $productoPedido->cantidad,
                            'precio_ud' => $productoPedido->precio_ud,
                            'precio_total' =>  ($productoPedido->cantidad * $productoPedido->precio_ud),
                            'iva' => $producto->iva != 0 ?  (($productoPedido->cantidad * $productoPedido->precio_ud) * $producto->iva / 100) : (($productoPedido->cantidad * $productoPedido->precio_ud) * 21 / 100) ,
                            'lote_id' => $lote,
                            'peso_kg' =>  $peso,
                            'unidades' => $productoPedido->unidades,
                            
                        ];
                    }

                }


            }

            $totalRectificado = 0;
            $base_imponible_rectificado = 0;
            $iva_productos_rectificado = 0;
            //dd($arrProductosFactura);
            foreach ($arrProductosFactura as $producto) {
                $base_imponible_rectificado += $producto['precio_total'];
                $iva_productos_rectificado += $producto['iva'];
                
            }

            //dd($base_imponible_rectificado);
            $totalRectificado = $base_imponible_rectificado + $iva_productos_rectificado;
            $total = $factura->total - $totalRectificado;
            $base_imponible = $factura->precio - $base_imponible_rectificado;
            $iva_productos = $factura->iva_total_pedido - $iva_productos_rectificado;

            //dd($totalRectificado, $base_imponible_rectificado, $iva_productos_rectificado, $total, $base_imponible, $iva_productos);

            //dd($productos);

            //comparar ids entre productos y productos de la factura y si coinciden, restarle la cantidad de productos factura a productos
            foreach($productos as $index => $producto){
                
                foreach($arrProductosFactura as $productoFactura){
                    
                    if(($producto['id'] == $productoFactura['id']) && ($producto['cantidad'] == $productoFactura['unidades'] )  ){
                       
                        $productos[$index]['cantidad'] -= $productoFactura['cantidad'];
                        $productos[$index]['precio_total'] -= $productoFactura['precio_total'];
                        $productos[$index]['iva'] = $producto['iva'] != 0 ?  (($productos[$index]['precio_total']) * $producto['iva'] / 100) : (($productos[$index]['precio_total']) * 21 / 100) ;
                        //dd($producto['cantidad']);
                    }
                }
            }


            //sumar 

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
            $pdf = Pdf::loadView('livewire.facturas.pdf2-component', $datos)->setPaper('a4', 'vertical');
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
                // "factura_{$factura->numero_factura}.pdf");
                "{$factura->numero_factura}.pdf"
            );


        } else {
            return redirect('admin/facturas');
        }

    }



    public function pdfIntermedio($id, $iva){

        $factura = Facturas::find($id);
        $configuracion = Configuracion::first();

        $facturasRectificativas = Facturas::where('factura_id', $id)->get();


        if ($factura != null) {
            $cliente = Clients::find($factura->cliente_id);
            $productos = [];
           
            //dd($albaran);
           
            $arrProductosFactura = [];
            
            foreach ($facturasRectificativas as $facturaRectificativa){
                $productosdeFactura = [];
                $productosFactura = DB::table('productos_factura')->where('factura_id', $facturaRectificativa->id)->get();

                foreach($productosFactura as $productoPedido){
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
                        $arrProductosFactura[] = [
                            'id' => $producto->id,
                            'nombre' => $producto->nombre,
                            'cantidad' => -$productoPedido->cantidad,
                            'precio_ud' => $productoPedido->precio_ud,
                            'precio_total' =>  ($productoPedido->cantidad * $productoPedido->precio_ud),
                            'iva' => $producto->iva != 0 ?  (($productoPedido->cantidad * $productoPedido->precio_ud) * $producto->iva / 100) : (($productoPedido->cantidad * $productoPedido->precio_ud) * 21 / 100) ,
                            'lote_id' => $lote,
                            'peso_kg' =>  $peso,
                        ];
                    }

                }


            }

            $totalRectificado = 0;
            $base_imponible_rectificado = 0;
            $iva_productos_rectificado = 0;
            //dd($arrProductosFactura);
            foreach ($arrProductosFactura as $producto) {
                $base_imponible_rectificado += $producto['precio_total'];
                $iva_productos_rectificado += $producto['iva'];
                
            }

            //dd($base_imponible_rectificado);
            $totalRectificado = $base_imponible_rectificado + $iva_productos_rectificado;
            $total = -$totalRectificado;
            $base_imponible = -$base_imponible_rectificado;
            $iva_productos = -$iva_productos_rectificado;

            //dd($totalRectificado, $base_imponible_rectificado, $iva_productos_rectificado, $total, $base_imponible, $iva_productos);

            //dd($productos);
            //comparar ids entre productos y productos de la factura y si coinciden, restarle la cantidad de productos factura a productos
            // foreach($productos as $index => $producto){
            //     foreach($arrProductosFactura as $productoFactura){
            //         if($producto['id'] == $productoFactura['id']){
            //             $productos[$index]['cantidad'] -= $productoFactura['cantidad'];
            //             $productos[$index]['precio_total'] -= $productoFactura['precio_total'];
            //             $productos[$index]['iva'] = $producto['iva'] != 0 ?  (($productos[$index]['precio_total']) * $producto['iva'] / 100) : (($productos[$index]['precio_total']) * 21 / 100) ;
            //             //dd($producto['cantidad']);
            //         }
            //     }
            // }


            //sumar 
                //dd($productosdeFactura);

            $datos = [
                'conIva' => $iva,
                'factura' => $factura,
                'cliente' => $cliente,
                'productos' => $arrProductosFactura,
                'configuracion' => $configuracion,
                'servicios' => $servicios ?? null,
                'productosFactura' => $productosdeFactura,
                'total' => $total,
                'base_imponible' => $base_imponible,
                'iva_productos' => $iva_productos,
                'facturasRectificativas' => $facturasRectificativas,
                
            ];
            
            // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
            $pdf = Pdf::loadView('livewire.facturas.pdf4-component', $datos)->setPaper('a4', 'vertical');
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
                // "factura_{$factura->numero_factura}.pdf");
                "{$facturasRectificativas[0]->numero_factura}.pdf"
            );


        } else {
            return redirect('admin/facturas');
        }

    }






    public function hasRectificativa($facturaId)
    {
        $factura = Facturas::find($facturaId);
        $facturaRectificativa = Facturas::where('factura_id', $facturaId)->get();

        return count($facturaRectificativa) > 0;
       
    }

    public function enviarRecordatorio($id, $tipo){


        $iva = true;
        $factura = Facturas::find($id);
        $configuracion = Configuracion::first();

        $facturasRectificativas = Facturas::where('factura_id', $id)->get();
        if($facturasRectificativas->count() > 0){

            if ($factura != null) {
                $pedido = Pedido::find($factura->pedido_id);
                $albaran =  Albaran::where('pedido_id', $factura->pedido_id)->first();
                $cliente = Clients::find($factura->cliente_id);
                // if($cliente->delegacion){
                //     if ($cliente->delegacion && in_array($cliente->delegacion['id'], [15, 14, 13, 7])) {
                //         $iva = false;
                //     }
                // }
                $delegacionNombre = $cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene
                if($delegacionNombre == '07 CANARIAS' || $delegacionNombre == '13 GIBRALTAR' || $delegacionNombre == '14 CEUTA' || $delegacionNombre == '15 MELILLA'){
                    $iva = false;
                }

                    
                $productofact = Productos::find($factura->producto_id);
                $productos = [];
               
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
                                'id' => $producto->id,
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
                $arrProductosFactura = [];
                
                foreach ($facturasRectificativas as $facturaRectificativa){
                    $productosdeFactura = [];
                    $productosFactura = DB::table('productos_factura')->where('factura_id', $facturaRectificativa->id)->get();
    
                    foreach($productosFactura as $productoPedido){
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
                            $arrProductosFactura[] = [
                                'id' => $producto->id,
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
    
    
                }
    
                $totalRectificado = 0;
                $base_imponible_rectificado = 0;
                $iva_productos_rectificado = 0;
    
                foreach ($arrProductosFactura as $producto) {
                    $base_imponible_rectificado += $producto['precio_total'];
                    $iva_productos_rectificado += $producto['iva'];
                    
                }
    
                $totalRectificado = $base_imponible_rectificado + $iva_productos_rectificado;
                $total = $factura->total - $totalRectificado;
                $base_imponible = $factura->precio - $base_imponible_rectificado;
                $iva_productos = $factura->iva_total_pedido - $iva_productos_rectificado;
    
    
                //dd($productos);
                //comparar ids entre productos y productos de la factura y si coinciden, restarle la cantidad de productos factura a productos
                foreach($productos as $index => $producto){
                    foreach($arrProductosFactura as $productoFactura){
                        if($producto['id'] == $productoFactura['id']){
                            $productos[$index]['cantidad'] -= $productoFactura['cantidad'];
                            $productos[$index]['precio_total'] -= $productoFactura['precio_total'];
                            $productos[$index]['iva'] = $producto['iva'] != 0 ?  (($productos[$index]['precio_total']) * $producto['iva'] / 100) : (($productos[$index]['precio_total']) * 21 / 100) ;
                            //dd($producto['cantidad']);
                        }
                    }
                }
                $productosMarketing = ProductosMarketingPedido::where('pedido_id', $pedido->id)->get();
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
                    'tipo' => $tipo,
                    'rectificada' => true,
                    'productosMarketing' => $productosMarketing,

                ];
                
                // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
                $pdf = Pdf::loadView('livewire.facturas.pdf2-component', $datos)->setPaper('a4', 'vertical');
                $pdf->render();

                $totalPages = $pdf->getCanvas()->get_page_count();
    
                $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                    $text = "Página $pageNumber de $totalPages";
                    $font = $fontMetrics->getFont('Helvetica', 'normal');
                    $size = 10;
                    $width = $canvas->get_width();
                    $canvas->text($width - 100, 15, $text, $font, $size);
                });
                //$pdf->output();
            }

           
        }else{

            if ($factura != null) {
                $pedido = Pedido::find($factura->pedido_id);
                $albaran =  Albaran::where('pedido_id', $factura->pedido_id)->first();
                $cliente = Clients::find($factura->cliente_id);
                // if($cliente->delegacion){

                //     if ($cliente->delegacion && in_array($cliente->delegacion['id'], [15, 14, 13, 7])) {
                //         $iva = false;
                //     }
                // }
                $delegacionNombre = $cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene
                if($delegacionNombre == '07 CANARIAS' || $delegacionNombre == '13 GIBRALTAR' || $delegacionNombre == '14 CEUTA' || $delegacionNombre == '15 MELILLA'){
                    $iva = false;
                }
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
                //$iva = true;
                if ($factura->tipo == 2){
                    
                    foreach ($productosdeFactura as $producto) {
                        $base_imponible += $producto['precio_total'];
                        $iva_productos += $producto['iva'];
                    }
                    $total = $base_imponible + $iva_productos;
    
                }
                // if($cliente->delegacion){

                //     if ($cliente->delegacion && in_array($cliente->delegacion['id'], [15, 14, 13, 7])) {
                //         $iva = false;
                //     }
                // }

                $delegacionNombre = $cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene
                if($delegacionNombre == '07 CANARIAS' || $delegacionNombre == '13 GIBRALTAR' || $delegacionNombre == '14 CEUTA' || $delegacionNombre == '15 MELILLA'){
                    $iva = false;
                }

                $productosMarketing = ProductosMarketingPedido::where('pedido_id', $pedido->id)->get();


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
                    'tipo' => $tipo,
                    'productosMarketing' => $productosMarketing,
                    
                ];
    
                $pdf = Pdf::loadView('livewire.facturas.pdf-component', $datos)->setPaper('a4', 'vertical');
                $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });

            //$pdf->output();
            }
            //dd($cliente->delegacion['COD'] , $iva);
        }
        if ($factura != null){
            try{
                //dd($datos);
                $emailsDireccion = [
                    // 'Alejandro.martin@serlobo.com',
                    // 'Sandra.lopez@serlobo.com'
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
                    Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new RecordatorioMail($pdf->output(), $datos));
                    //  Mail::to('ivan.mayol@hawkins.es')->cc('ivan.mayol@hawkins.es')->bcc( $emailsDireccion)->send(new RecordatorioMail($pdf->output(), $datos));

                    foreach($this->emailsSeleccionados as $email){
                        $registroEmail = new RegistroEmail();
                        $registroEmail->factura_id = $factura->id;
                        $registroEmail->pedido_id = null;
                        $registroEmail->cliente_id = $factura->cliente_id;
                        $registroEmail->email = $email;
                        $registroEmail->user_id = Auth::user()->id;
                        if($tipo == 'impago'){
                            $registroEmail->tipo_id = 5;
                        }else{
                            $registroEmail->tipo_id = 6;
                        }
                        $registroEmail->save();
                    }
    
                }else{
                    //dd($datos);
                    Mail::to($cliente->email)->bcc($emailsDireccion)->send(new RecordatorioMail($pdf->output(), $datos));
                    //  Mail::to('ivan.mayol@hawkins.es')->bcc('ivan.mayol@hawkins.es')->send(new RecordatorioMail($pdf->output(), $datos));

                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $cliente->email;
                    $registroEmail->user_id = Auth::user()->id;
                    if($tipo == 'impago'){
                        $registroEmail->tipo_id = 5;
                    }else{
                        $registroEmail->tipo_id = 6;
                    }
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
            $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });
            return $pdf;


        } 
    }


}
