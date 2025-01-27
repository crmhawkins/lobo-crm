<?php

namespace App\Http\Livewire\Facturas;


use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\Productos;
use App\Models\Clients;
use App\Models\Facturas;
use App\Mail\FacturaMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Alertas;
use App\Models\Iva;
use App\Models\StockEntrante;
use App\Models\ProductosFacturas;
use Carbon\Carbon;
use App\Models\ServiciosFacturas;
use App\Models\StockRegistro;
use App\Models\RegistroEmail;
use App\Models\Configuracion;
use App\Models\User;
use App\Models\Emails;
use App\Models\Almacen;
use App\Mail\TransporteRecogida;
use App\Models\TipoEmails;
use App\Models\ProductosMarketingPedido;
use App\Models\Retencion;


class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;


    public $numero_factura;
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descripcion;
    public $estado;
    public $metodo_pago;
    public $facturas;
    public $precio;
    public $pedido;
    public $pedido_id;
    public $cliente;
    public $clientes;
    public $cliente_id;
    public $producto_id;
    public $productos;
    public $cantidad;
    public $descuento;
    public $total;
    public $subtotal_pedido;
    public $iva_total_pedido;
    public $descuento_total_pedido;
    public $tipo;
    public $factura_id;
    public $productos_factura;
    public $productos_pedido = [];
    public $serviciosDB;
    public $servicios = [];
    public $descripcionServicio;
    public $cantidadServicio;
    public $importeServicio;
    public $recargo = 0;
    public $total_recargo = 0;
    public $registroEmails = [];
    public $emails = [];
    public $emailsSeleccionados = [];
    public $emailNuevo;
    public $destinos = [
        '1' => 'Algeciras',
        '2' => 'Córdoba',
        '3' => 'Madrid',
        '4' => 'Otro'
    ];

    public $destinosValue;
    public $otroDestino;
    public $emailTransporte;
    public $anotacionesEmail;
    public $observacionesEmail;
    public $retencion_id;
    public $retenciones;


    public function getTipo($id){

        $tipo = TipoEmails::find($id);
        if($tipo){
            return $tipo->nombre;
        }else{
            return '';
        }

    }

    public function mount()
    {
        
        $this->facturas = Facturas::find($this->identificador);
        $this->clientes = Clients::where('estado', 2)->get();
        $this->cliente_id = $this->facturas->cliente_id;
        $this->cliente = Clients::find($this->cliente_id);
        $this->pedido = Pedido::find($this->facturas->pedido_id);
        $this->productos = Productos::where('tipo_precio',5)->get();  
        $this->producto_id = $this->facturas->producto_id;
        $this->cantidad = $this->facturas->cantidad;
        $this->recargo = $this->facturas->recargo ?? 0;
        $this->total_recargo = $this->facturas->total_recargo ?? 0;
        $this->registroEmails = RegistroEmail::where('factura_id', $this->facturas->id)->get();
        $this->anotacionesEmail = $this->facturas->anotacionesEmail;
        $this->observacionesEmail = $this->facturas->observacionesEmail;
        if(isset($this->pedido)){
            $this->precio = $this->pedido->precio;
        }else{
            // dd($this->facturas);
            $this->precio = 0;
            $this->serviciosDB = ServiciosFacturas::where('factura_id', $this->facturas->id)->get();
           
            if($this->serviciosDB){
                
                foreach($this->serviciosDB as $servicio){
                    
                    $this->servicios= array_merge($this->servicios, [['descripcion' => $servicio->descripcion, 'cantidad' => $servicio->cantidad, 'importe' => $servicio->precio , 'id' => $servicio->id, 'new' => false]]);
                }
            }
        }
        //dd($this->pedido->precio);
        $this->pedido_id = $this->facturas->pedido_id;
        
       // dd($this->productos_pedido);

        $this->numero_factura = $this->facturas->numero_factura;

        $this->fecha_emision = $this->facturas->fecha_emision;
        $this->fecha_vencimiento = $this->facturas->fecha_vencimiento;

        $this->descripcion = $this->facturas->descripcion;
        $this->estado = $this->facturas->estado;
        $this->metodo_pago = $this->facturas->metodo_pago;
        $this->total = $this->facturas->total;
        $this->descuento_total_pedido = $this->facturas->descuento_total_pedido;
        $this->iva_total_pedido = $this->facturas->iva_total_pedido;
        $this->subtotal_pedido = $this->facturas->subtotal_pedido;
        $this->tipo = $this->facturas->tipo;
        if($this->tipo == 2){
            $this->precio = $this->facturas->precio;
        }
        
        if(!$this->facturas->descuento){
            if(isset($this->pedido)){
                if($this->pedido->descuento){
                    $this->descuento = $this->pedido->porcentaje_descuento;
                }
            }else{
                $this->descuento = 0;
        }
        }else{
            $this->descuento = $this->facturas->descuento;
        }

        if($this->tipo == 2){
            $this->productos = Productos::All();
            $this->productos_pedido = DB::table('productos_pedido')->where('pedido_id', $this->pedido_id)->get();
            $this->productos_factura = DB::table('productos_factura')->where('factura_id', $this->facturas->id)->get();
            $this->productos_pedido = json_decode(json_encode($this->productos_pedido), true);

            //dd($this->productos_pedido);
            //por cada producto de la factura debemos coger su cantidad y añadirla al json de productos_pedido bajo ['descontar_ud]
            foreach($this->productos_factura as $producto_factura) {
                foreach($this->productos_pedido as $index => $producto_pedido) {
                    // Comparamos el producto_id y unidades para asegurarnos de que sean los mismos
                    if ($producto_pedido['producto_pedido_id'] == $producto_factura->producto_id && $producto_pedido['unidades'] == $producto_factura->unidades) {
                        // Añadimos la cantidad a descontar_ud
                        $this->productos_pedido[$index]['descontar_ud'] = $producto_factura->cantidad;
                    }
                }
            }
            
        }

        $this->emails = Emails::where('cliente_id', $this->cliente_id)->get();

        $this->retenciones = Retencion::orderBy('dias_retencion', 'desc')->get();

        $this->retencion_id = $this->facturas->retencion_id;
        $this->total_original = $this->facturas->total_original;
        $this->total_retencion = $this->facturas->retencion ? ($this->facturas->total_original * $this->facturas->retencion->porcentaje / 100) : 0;

        if($this->retencion_id){
            $this->precio = $this->facturas->precio;
        }
        // dd($this->facturas , $this->precio);
        
    }


    public function updatedRetencionId($value)
    {
        if ($value === '') {
            $this->retencion_id = null;
        } else {
            $this->retencion_id = (int) $value;
        }
    
        
        $retencion = Retencion::find($value);
        $delegacion = $this->facturas->cliente->delegacion->nombre;
        if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR'){
            $this->total_original = $this->total_original  ? $this->total_original : $this->precio;
        }else{
            $this->total_original = $this->total_original  ? $this->total_original : $this->total;
        }
        if ($retencion) {

            $this->total_retencion = round($this->total_original * $retencion->porcentaje / 100, 2);
        } else {
            $this->total_retencion = 0;
        }
    }
   

    public function getPedido(){
        $pedido = Pedido::find($this->pedido_id);
        $factura = Facturas::find($this->facturas->id);
        $this->iva_total_pedido = $factura->iva_total_pedido;
        $this->descuento_total_pedido = $factura->descuento_total_pedido;
        $this->iva_total_pedido = $factura->iva_total_pedido;
        $this->subtotal_pedido = $factura->subtotal_pedido;
        $this->precio = $this->pedido->precio;
        $this->descuento = $pedido->porcentaje_descuento;
    }

    public function getCliente($id)
    {
        $cliente = Clients::find($id);
        if ($cliente) {
            return $cliente->nombre;
        } else {
            return '';
        }
    }

    public function getClienteDelegacion($id)
    {
        $cliente = Clients::find($id)->with('delegacion');
        dd($cliente->delegacion);
        if ($cliente) {
            return $cliente->delegacion_COD;
        } else {
            return '';
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user) {
            return $user->name;
        } else {
            return '';
        }
    }

    


    
    public function addArticulo()
    {
        
        $this->servicios = array_merge($this->servicios, [['descripcion' => $this->descripcionServicio, 'cantidad' => $this->cantidadServicio, 'importe' => $this->importeServicio, 'new' => true]]);
        $this->descripcionServicio = null;
        $this->cantidadServicio = null;
        $this->importeServicio = null;
        //dd($this->servicios);
    }
    public function deleteServicio($index)
    {
        //marcarlo como a eliminar
        if($this->servicios[$index]['new']){
            unset($this->servicios[$index]);
        }else{
            $this->servicios[$index]['delete'] = true;
        }
    }

    


    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first();
        if($nombre_producto){
            return $nombre_producto->nombre;
        }else{
            return '';
        }

    }
    public function getNumeroFactura(){
        $year = Carbon::now()->format('y'); // Esto obtiene el año en formato de dos dígitos, por ejemplo, "24" para 2024.
            $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)->where('tipo', 2)->max('numero_factura');

            if ($lastInvoice) {
                // Extrae el número secuencial de la última factura del año y lo incrementa
                $lastNumber = intval(substr($lastInvoice, 4)) + 1; // Asume que el formato es siempre "F24XXXX"
            } else {
                if($year = 24 ){
                    $lastNumber = 20;
                }else{
                    $lastNumber = 1;
                }
            }
           
        
         
            $this->numero_factura = 'CN' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        
        //dd("prueba"); 

    }

    public function getUnidadesTabla($id)
    {

        $producto = Productos::find($this->productos_pedido[$id]['producto_pedido_id']);
        
        $cajas = ($this->productos_pedido[$id]['unidades'] / $producto->unidades_por_caja);
        $pallets = floor($cajas / $producto->cajas_por_pallet);
        $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
        $unidades = '';
        if ($cajas_sobrantes > 0) {
            $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
        } else {
            $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets)';
        }
        return $unidades;
    }

    public function render()
    {
        

        // $this->tipoCliente == 0;
        return view('livewire.facturas.edit-component');
    }

    public function calculoPrecio()
    {
        if(isset($this->cantidad) && isset($this->producto_id)){
           $producto = $this->productos->find($this->producto_id);
           if(isset($producto)){
           $this->precio = $producto->precio * $this->cantidad;
        }
        }
    }

    public function hasStockEntrante($lote_id){
        $stockEntrante = StockEntrante::where('id', $lote_id)->first();
        //  dd($stockEntrante);
        if($stockEntrante){
            return true;
        }else{
            return false;
        }
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate(
            [
                'numero_factura' => 'required',
                'cliente_id' => 'required',
                'pedido_id' => 'nullable',
                'fecha_emision' => 'required',
                'fecha_vencimiento' => '',
                'descripcion' => '',
                'estado' => 'nullable',
                'precio' => 'nullable',
                'total' => 'nullable',
                'metodo_pago' => 'nullable',
                'producto_id' => 'nullable',
                'cantidad' => 'nullable',
            ],
            // Mensajes de error
            [
                'numero_factura.required' => 'Indique un nº de factura.',
                'fecha_emision.required' => 'Ingrese una fecha de emisión',

            ]
        );

        if($this->tipo == 2){
            //recorremos los productos de la factura y miramos si las cantidades son menores o mayores que las descontar_ud
           // Recorremos los productos de la factura
            foreach($this->productos_factura as $producto_factura) {
                // Buscamos el producto correspondiente en productos_pedido
                foreach ($this->productos_pedido as $producto_pedido) {
                    // Comparamos por producto_id y lote_id para asegurarnos de que estamos comparando los productos correctos
                    if ($producto_factura['producto_id'] == $producto_pedido['producto_pedido_id'] && $producto_factura['stock_entrante_id'] == $producto_pedido['lote_id']) {
                        // Verificamos si la cantidad a descontar existe
                        if (!isset($producto_pedido['descontar_ud'])) {
                            continue;
                        }

                        $stockEntrante = StockEntrante::where('id', $producto_pedido['lote_id'])->first();

                        if ($producto_factura['cantidad'] > $producto_pedido['descontar_ud']) {
                            // Si la cantidad de la factura es mayor que la cantidad a descontar, se añade la diferencia al stock
                            $registroStock = new StockRegistro();
                            $registroStock->stock_entrante_id = $stockEntrante->id;
                            $registroStock->cantidad = ($producto_factura['cantidad'] - $producto_pedido['descontar_ud']);
                            $registroStock->tipo = "devolucion editada";
                            $registroStock->factura_id = $this->facturas->id;
                            $registroStock->motivo = "Salida";
                            $registroStock->save();

                            // Actualizar ProductoFactura con la cantidad descontada
                            $producto_factura_model = ProductosFacturas::where('producto_id', $producto_pedido['producto_pedido_id'])
                                ->where('factura_id', $this->facturas->id)
                                ->first();
                            $producto_factura_model->cantidad = $producto_pedido['descontar_ud'];
                            $producto_factura_model->save();
                        } elseif ($producto_factura['cantidad'] < $producto_pedido['descontar_ud']) {
                            // Si la cantidad de la factura es menor que la cantidad a descontar, se resta la diferencia al stock
                            $registroStock = new StockRegistro();
                            $registroStock->stock_entrante_id = $stockEntrante->id;
                            $registroStock->cantidad = -($producto_pedido['descontar_ud'] - $producto_factura['cantidad']);
                            $registroStock->tipo = "devolucion editada";
                            $registroStock->motivo = "Entrada";
                            $registroStock->factura_id = $this->facturas->id;
                            $registroStock->save();

                            // Actualizar ProductoFactura con la cantidad descontada
                            $producto_factura_model = ProductosFacturas::where('producto_id', $producto_pedido['producto_pedido_id'])
                                ->where('factura_id', $this->facturas->id)
                                ->first();
                            $producto_factura_model->cantidad = $producto_pedido['descontar_ud'];
                            $producto_factura_model->save();
                        }
                        break; // Salimos del bucle interno si encontramos el producto correspondiente
                    }
                }
            }



            //si hay productos pedido con descontar_ud que no estan en productos_factura, se añaden al stock y a productos_factura
            foreach($this->productos_pedido as $index => $producto_pedido){
                $producto_factura = ProductosFacturas::where('producto_id', $producto_pedido['producto_pedido_id'])->where('factura_id', $this->facturas->id)->first();
                //dd($producto_factura);
                if(!$producto_factura && isset($producto_pedido['descontar_ud']) && $producto_pedido['descontar_ud'] > 0){
                    $stockEntrante = StockEntrante::where('id', $producto_pedido['lote_id'])->first();
                    if($stockEntrante){

                        $registroStock = new StockRegistro();
                        $registroStock->stock_entrante_id = $stockEntrante->id;
                        $registroStock->cantidad = - $producto_pedido['descontar_ud'];
                        $registroStock->tipo = "devolucion";
                        $registroStock->motivo = "Entrada";
                        $registroStock->factura_id = $this->facturas->id;
                        $registroStock->save();
                        

                        $this->productos_factura->push([
                            'producto_pedido_id' => $producto_pedido['producto_pedido_id'],
                                'cantidad' => $producto_pedido['descontar_ud'],
                        ]);

                        $productosFactura = new ProductosFacturas();
                        $productosFactura->factura_id = $this->facturas->id;
                        $productosFactura->producto_id = $producto_pedido['producto_pedido_id'];
                        $productosFactura->cantidad = $producto_pedido['descontar_ud'];
                        $productosFactura->unidades = $producto_pedido['unidades'];
                        $productosFactura->precio_ud = $producto_pedido['precio_ud'];
                        $total = $producto_pedido['precio_total'];
                        $productosFactura->total = $total;
                        $productosFactura->stock_entrante_id = $stockEntrante->id;
                        $productosFactura->save();
                }
                    
                }
            }


            
            $factura = Facturas::find($this->facturas->id);
            $factura::where('id', $this->facturas->id)->update(['cliente_id' => $this->cliente_id]);

    
    
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
    
                //$facturaRectificativa
                //dd($total , $base_imponible, $iva_productos);
                
                
            $pedido = Pedido::find($this->facturas->pedido_id);
            $factura = Facturas::where('id', $this->facturas->factura_id)->first();
            $facturaRectificativa = Facturas::where('id', $this->facturas->id)->first();


            //cambiar total factura, iva total e importe.
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
                
                if ($facturaRectificativa){
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

                $facturaRectificativa->precio = $base_imponible;
                $facturaRectificativa->iva_total_pedido = $iva_productos;
                $facturaRectificativa->total = $total;
                $facturaRectificativa->save();






    
    
            } 

            //alerta
            $this->alert('success', 'Factura actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
            //dd("prueba");


        }elseif($this->tipo == 3){
                //si es un servicio
                //dd("prueba");
                foreach($this->servicios as $index => $servicio){
                    if($servicio['new']){
                        $servicioFactura = new ServiciosFacturas();
                        $servicioFactura->factura_id = $this->facturas->id;
                        $servicioFactura->descripcion = $servicio['descripcion'];
                        $servicioFactura->cantidad = $servicio['cantidad'];
                        $servicioFactura->precio = $servicio['importe'];
                        $servicioFactura->total = $servicio['importe'] * $servicio['cantidad'];
                        $servicioFactura->save();
                    }else if(isset($servicio['delete'])){
                        $servicioFactura = ServiciosFacturas::find($servicio['id']);
                        $servicioFactura->delete();
                    }
                }
                $servicios = ServiciosFacturas::where('factura_id', $this->facturas->id)->get();
                //calcular total y subtotalPedido
                $total = 0;
                $precio = 0;
                foreach($servicios as $servicio){
                    
                    $total += $servicio->total;
                    
                }



                $this->facturas->precio = $total;
                
                $this->facturas->iva_total_pedido = $total * 21 / 100;
                $this->facturas->iva = $this->facturas->iva_total_pedido;
                $this->facturas->total = $total + ($total * 21 / 100);
                $this->facturas->subtotal_pedido = $total;
                $this->facturas->fecha_emision = $this->fecha_emision;
                $this->facturas->fecha_vencimiento = $this->fecha_vencimiento;
                $this->facturas->estado = $this->estado;
                $this->facturas->descripcion = $this->descripcion;
                $this->facturas->cliente_id = $this->cliente_id;    
                $this->facturas->save();

                $this->alert('success', 'Factura actualizada correctamente!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'confirmed',
                    'confirmButtonText' => 'ok',
                    'timerProgressBar' => true,
                ]);
            
        }else{
            // Guardar datos validados



            // dd("hola");



            //$pedido = Pedido::find($this->pedido_id);
            $this->getPedido();
            
            
            $facturasSave = $this->facturas->update([
                'numero_factura' => $this->numero_factura,
                'cliente_id' => $this->cliente_id,
                'pedido_id'  => $this->pedido_id,
                'fecha_emision' => $this->fecha_emision,
                'fecha_vencimiento' => $this->fecha_vencimiento,
                'descripcion' => $this->descripcion,
                'estado' => $this->estado,
                'precio' => $this->precio,
                'total' => $this->total,
                'metodo_pago' => $this->metodo_pago,
                'cantidad' => $this->cantidad,
                'producto_id' =>$this->producto_id,
                'descuento' =>$this->descuento,
                'descuento_total_pedido' => $this->descuento_total_pedido,
                'iva_total_pedido' => $this->iva_total_pedido,
                'subtotal_pedido' => $this->subtotal_pedido,
                'retencion_id' => isset($this->retencion_id) ? $this->retencion_id : null,
                'total_original' => isset($this->total_original) ? $this->total_original : null,


            ]);

            // dd($this->facturas);

            if($this->facturas->estado == "Pagado"){
                $pedido=Pedido::find($this->pedido_id);
                if (isset($this->pedido_id) && isset($pedido)){
                    $pedido->update(['estado' => 6]);
                    }
            }

            if ($facturasSave) {
                $this->calcularTotales($this->facturas);
                $this->alert('success', 'Factura actualizada correctamente!', [
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

        session()->flash('message', 'Factura actualizada correctamente.');

            $this->emit('productUpdated');
        
    }
    public function changeDescontar($id){
        // si descontar es mayor que unidades, descontar = unidades
        if($this->productos_pedido[$id]['descontar_ud'] > $this->productos_pedido[$id]['unidades']){
            $this->productos_pedido[$id]['descontar_ud'] = $this->productos_pedido[$id]['unidades'];
        }

        // si descontar es menor que 0, descontar = 0
        if($this->productos_pedido[$id]['descontar_ud'] < 0){
            $this->productos_pedido[$id]['descontar_ud'] = 0;
        }
    }
    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar el la factura? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'update',
            'confirmDelete',
            'aceptarFactura',
            'cancelarFactura',
            'imprimirFacturaIva',
            'imprimirFactura',
            'listarPresupuesto',
            'enviarTransporte'
        ];
    }
    public function aceptarFactura()
    {
        $this->pedido->update(['estado' => 6]);
        $presupuesosSave = $this->facturas->update(['estado' => 'Facturada']);

        // Alertas de guardado exitoso
        if ($presupuesosSave) {

            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Facturado ',
                'descripcion' => 'Se cobro el pedido nº ' . $this->pedido->id ,
                'referencia_id' => $this->pedido->id,
                'leida' => null,
            ]);

            $dComercial = User::where('id', 14)->first();
            $dGeneral = User::where('id', 13)->first();
            $administrativo1 = User::where('id', 17)->first();
            $administrativo2 = User::where('id', 18)->first();
            $almacenAlgeciras = User::where('id', 16)->first();
            $almacenCordoba = User::where('id', 15)->first();
            $data = [['type' => 'text', 'text' => $this->pedido->id]];
            $buttondata = [$this->pedido->id];

            if(isset($dComercial) && $dComercial->telefono != null){
                $phone = '+34'.$dComercial->telefono;
                enviarMensajeWhatsApp('pedido_facturado', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_facturado', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_facturado', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_facturado', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null &&  $this->pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_facturado', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null &&  $this->pedido->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_facturado', $data, $buttondata, $phone);
            }

            $this->alert('success', '¡Presupuesto aceptado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido aceptar el presupuesto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function cancelarFactura()
    {
        // Guardar datos validados
        $presupuesosSave = $this->facturas->update(['estado' => 'Cancelada']);


        // Alertas de guardado exitoso
        if ($presupuesosSave) {
            $this->alert('success', '¡Presupuesto cancelado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido cancelar el presupuesto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }


    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('facturas.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $factura = Facturas::find($this->identificador);

        //event(new \App\Events\LogEvent(Auth::user(), 19, $factura->id));
        //no borrar la factura, dejarla con deleted_at para mantener la integridad de los datos
        $factura->delete();


        //$factura->delete();
        return redirect()->route('facturas.index');
    }

    public function imprimirFacturaIva()
    {

        //dd($this->emailsSeleccionados);

        $factura = Facturas::find($this->identificador);
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

            $productosMarketing = $pedido != null ? ProductosMarketingPedido::where('pedido_id', $pedido->id)->get() : [];

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
                'anotacionesEmail' => $this->anotacionesEmail,
                'productosMarketing' => $productosMarketing,	
                
            ];

            //update factura con anotaciones email
            $factura = Facturas::find($this->identificador);
            $factura->anotacionesEmail = $this->anotacionesEmail;
            $factura->save();
            
            //dd($datos);
        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos)->setPaper('a4', 'vertical');
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
        try{

            $emailsDireccion = [
                 'Alejandro.martin@serlobo.com',
                 'Ivan.ruiz@serlobo.com',
                 'Administracion@serlobo.com',
                 'Sandra.lopez@serlobo.com',
                 'vanessa.casanova@serlobo.com'
            ];

            $cliente = Clients::find($factura->cliente_id);
            if($cliente != null && $cliente->comercial_id != null){
                $comercial = User::find($cliente->comercial_id);
                if($comercial != null && $comercial->email != null){
                    $emailsDireccion[] = $comercial->email;
                }
            }
            

            if(count($this->emailsSeleccionados) > 0){
                if($this->emailNuevo != null){
                    array_push($this->emailsSeleccionados, $this->emailNuevo);
                }

                Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new FacturaMail($pdf->output(), $datos));
                // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));
                foreach($this->emailsSeleccionados as $email){
                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $email;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->tipo_id = 1;
                    $registroEmail->save();
                }

                if($this->emailNuevo != null){

                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $this->emailNuevo;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->tipo_id = 1;
                    $registroEmail->save();
                }
            }else{

                if($this->emailNuevo != null){
                    Mail::to($this->emailNuevo)->cc($this->emailNuevo)->bcc($emailsDireccion)->send(new FacturaMail($pdf->output(), $datos));
                    // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));
                }else{
                    Mail::to($cliente->email)->bcc($emailsDireccion)->send(new FacturaMail($pdf->output(), $datos));
                    // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));
                }


                $registroEmail = new RegistroEmail();
                $registroEmail->factura_id = $factura->id;
                $registroEmail->pedido_id = null;
                $registroEmail->cliente_id = $factura->cliente_id;
                $registroEmail->email = $cliente->email;
                $registroEmail->user_id = Auth::user()->id;
                $registroEmail->tipo_id = 1;
                $registroEmail->save();

                if($this->emailNuevo != null){
                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id ;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $this->emailNuevo;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->tipo_id = 1;
                    $registroEmail->save();
                }
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

        /*return response()->streamDownload(
            fn () => print($pdf->output()),
            "factura_{$factura->id}.pdf");*/
        }else{
            return redirect('admin/facturas');
        }

    }



    public function enviarTransporte(){
        
        //si emailTransporte es null, alert
        if($this->emailTransporte == null){
            $this->alert('error', '¡No se ha podido enviar la factura por email!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        //si destino es null o 0, alert
        if($this->destinosValue == null || $this->destinosValue == 0){
            $this->alert('error', '¡Destino sin seleccionar!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        //si destino es 4, ver si otroDestino es null, alert
        if($this->destinosValue == 4 && $this->otroDestino == null){
            $this->alert('error', '¡Indique otro destino!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }
        $almacen = null;
        switch($this->destinosValue){
            case 1:
                $almacen = Almacen::where('almacen', 'Algeciras')->first();
                //dd($almacen);
                if($almacen == null){
                    $this->alert('error', '¡Almacen no encontrado!', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                    ]);
                    return;
                }

                $this->otroDestino = $almacen->direccion;
            break;
            case 2:
                $almacen = Almacen::where('almacen', 'Córdoba')->first();

                if($almacen == null){
                    $this->alert('error', '¡Almacen no encontrado!', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                    ]);
                    return;
                }

                $this->otroDestino = $almacen->direccion;
            break;
            case 3:
                $almacen = Almacen::where('almacen', 'Madrid')->first();

                if($almacen == null){
                    $this->alert('error', '¡Almacen no encontrado!', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                    ]);
                    return;
                }

                $this->otroDestino = $almacen->direccion;
            break;
            case 4:
                $this->otroDestino = $this->otroDestino;
            break;



        }



        $factura = Facturas::find($this->identificador);
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
                        'num_cajas' => isset($producto->unidades_por_caja) ? floor($productoPedido->cantidad / $producto->unidades_por_caja) : null,
                        'num_pallet' => isset($producto->cajas_por_pallet) ? floor(($productoPedido->cantidad / $producto->unidades_por_caja) / $producto->cajas_por_pallet) : null,
                        'productos_caja' => isset($producto->unidades_por_caja) ? $producto->unidades_por_caja : null,
                        'productos_pallet' => isset($producto->cajas_por_pallet) ? $producto->cajas_por_pallet : null,
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
            $almacenOrigen = Almacen::where('id', $pedido->almacen_id)->first();
            //dd($productosdeFactura);
            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'num_albaran' => $num_albaran = $albaran->num_albaran,
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
                'destino' => $this->otroDestino,
                'observacionesEmail' => $this->observacionesEmail,
                'hasproductosFactura' => true,
                'almacen' => $almacenOrigen,
                
            ];

            //update factura con anotaciones email
            $factura = Facturas::find($this->identificador);
            $factura->observacionesEmail = $this->observacionesEmail;
            $factura->save();
            
            //dd($datos);
        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf3-component',$datos)->setPaper('a4', 'vertical');
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
        try{

            $emailsDireccion = [
                 'Alejandro.martin@serlobo.com',
                 'Administracion@serlobo.com',
                 'vanessa.casanova@serlobo.com',
                 'Sandra.lopez@serlobo.com'
            ];

            Mail::to($this->emailTransporte)->cc($this->emailTransporte)->bcc($emailsDireccion)->send(new TransporteRecogida($pdf->output(), $datos));
                
            $registroEmail = new RegistroEmail();
            $registroEmail->factura_id = $factura->id;
            $registroEmail->pedido_id = null;
            $registroEmail->cliente_id = $factura->cliente_id;
            $registroEmail->email = $this->emailTransporte;
            $registroEmail->user_id = Auth::user()->id;
            $registroEmail->tipo_id = 4;
            $registroEmail->save();

            
            $this->alert('success', '¡Enviado al transportista correctamente!', [
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

        /*return response()->streamDownload(
            fn () => print($pdf->output()),
            "factura_{$factura->id}.pdf");*/
        }else{
            return redirect('admin/facturas');
        }



    }



    public function calcularTotales($factura){
        $iva= 0;
        $total = 0;
        $recargo = $this->recargo;
        $recargo_total = 0;
        //busca la factura en la base de datos que debe estar actualizada
        $factura = Facturas::find($factura->id);
        //dd($factura);
        //dd($factura);
        //si hay pedido id
        if(isset($factura) && isset($factura->pedido_id) && $factura->pedido_id != null){
            $recargo_total = (($factura->precio * $recargo) / 100);
            //coger el precio del pedido y sumarle el iva
            $total = $factura->precio + $recargo_total +  $factura->iva_total_pedido;
            //dd($factura->precio);
            $iva = $factura->iva_total_pedido;
            //dd($iva);
            $factura->iva = $iva;
            $factura->total = $total;
            $factura->recargo = $recargo;
            $factura->total_recargo = $recargo_total;
            $factura->total = $total;

            if ($factura->retencion_id != null) {
                $delegacion =$factura->cliente->delegacion->nombre;
                if ($delegacion) {
                    // Determinar el valor base para el cálculo
                    $valorBase = ($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR') 
                        ? $factura->precio 
                        : $factura->total;
                    $retencion = Retencion::find($factura->retencion_id);
                    
                    // Establecer el total original si aún no está definido
                    $factura->total_original = $factura->total_original ?? $valorBase;
                    
                    // Calcular el total con retención si aún no está definido
                    $total_retencion = ($factura->total_original + ($factura->total_original * $retencion->porcentaje / 100));
                    // dd($this->total_retencion);
                    // dd($this->total_original);
                   if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR'){
                        $factura->precio = $total_retencion;
                   }else{
                        $factura->total =  $total_retencion;
                   }


                //    dd($this->precio , $this->total);
                    // Actualizar el total de la factura
                }
            }

            //dd($factura);
            $factura->save();
            // dd($factura);
            $this->total =  round($factura->total, 2);
            $this->iva_total_pedido = round($factura->iva, 2);
            $this->precio = round($factura->precio, 2);
            
        }else{
            if(isset($factura) && isset($factura->precio) && $factura->precio != null){
                $recargo_total = (($factura->precio * $recargo) / 100);
                $total = $factura->precio;
                $iva = (($factura->precio * 21) / 100);
                if($factura->descuento){
                    $total = $total - (($total * $factura->descuento) / 100) + $recargo_total;
                }else{
                    $total = $total + $recargo_total;
                }

                //total es total + iva
                $total = $total + $iva;

                $factura->iva = $iva;
                $factura->total = $total;
                $factura->recargo = $recargo;
                $factura->total_recargo = $recargo_total;
                $factura->save();

            }
            


        }

    }

    public function imprimirFactura()
    {

        $factura = Facturas::find($this->identificador);
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
            $iva = false;
            if ($factura->tipo == 2){
                
                foreach ($productosdeFactura as $producto) {
                    $base_imponible += $producto['precio_total'];
                    $iva_productos += $producto['iva'];
                }
                $total = $base_imponible + $iva_productos;

            }
            $productosMarketing = $pedido != null ? ProductosMarketingPedido::where('pedido_id', $pedido->id)->get() : [];

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
                'anotacionesEmail' => $this->anotacionesEmail,
                'productosMarketing' => $productosMarketing,
                
            ];

            //update factura con anotaciones email
            $factura = Facturas::find($this->identificador);
            $factura->anotacionesEmail = $this->anotacionesEmail;
            $factura->save();
            

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos)->setPaper('a4', 'vertical');
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
        try{

            $emailsDireccion = [
                'Alejandro.martin@serlobo.com',
                'Ivan.ruiz@serlobo.com',
                'Administracion@serlobo.com',
                'Sandra.lopez@serlobo.com',
                'vanessa.casanova@serlobo.com'
            ];

            $cliente = Clients::find($factura->cliente_id);
            if($cliente != null && $cliente->comercial_id != null){
                $comercial = User::find($cliente->comercial_id);
                if($comercial != null && $comercial->email != null){
                    $emailsDireccion[] = $comercial->email;
                }
            }

            if(count($this->emailsSeleccionados) > 0){

                if($this->emailNuevo != null){
                    array_push($this->emailsSeleccionados, $this->emailNuevo);
                }
    
                Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new FacturaMail($pdf->output(), $datos));
                // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));


                foreach($this->emailsSeleccionados as $email){
                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $email;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->tipo_id = 1;
                    $registroEmail->save();
                }


                if($this->emailNuevo != null){

                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id =$factura->id;
                    $registroEmail->pedido_id = null ;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $this->emailNuevo;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->tipo_id = 1;
                    $registroEmail->save();
                }

            }else{

                if($this->emailNuevo != null){
                    Mail::to($cliente->email)->cc($this->emailNuevo)->bcc($emailsDireccion)->send(new FacturaMail($pdf->output(), $datos));
                    // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));
                }else{
                    Mail::to($cliente->email)->bcc($emailsDireccion)->send(new FacturaMail($pdf->output(), $datos));
                    // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));
                }


                $registroEmail = new RegistroEmail();
                $registroEmail->factura_id = $factura->id;
                $registroEmail->pedido_id = null;
                $registroEmail->cliente_id = $factura->cliente_id;
                $registroEmail->email = $cliente->email;
                $registroEmail->user_id = Auth::user()->id;
                $registroEmail->tipo_id = 1;
                $registroEmail->save();

                if($this->emailNuevo != null){
                    Mail::to($this->emailNuevo)->send(new FacturaMail($pdf->output(), $datos));
                    // Mail::to('ivan.mayol@hawkins.es')->send(new FacturaMail($pdf->output(), $datos));
                    $registroEmail = new RegistroEmail();
                    $registroEmail->factura_id = $factura->id;
                    $registroEmail->pedido_id = null;
                    $registroEmail->cliente_id = $factura->cliente_id;
                    $registroEmail->email = $this->emailNuevo;
                    $registroEmail->user_id = Auth::user()->id;
                    $registroEmail->tipo_id = 1;
                    $registroEmail->save();
                }

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
        




        /*return response()->streamDownload(
            fn () => print($pdf->output()),
            "factura_{$factura->id}.pdf");*/
        }else{
            return redirect('admin/facturas');
        }
    }

}
