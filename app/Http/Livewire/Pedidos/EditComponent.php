<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Almacen;
use App\Models\PedidosStatus;
use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\Clients;
use App\Models\Pedido;
use App\Mail\PedidoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Alertas;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Facturas;
use App\Models\Iva;
use App\Models\ProductoPedido;
use App\Models\RegistroEmail;
use App\Models\User;
use App\Models\Emails;
use App\Models\GestionPedidos;
use App\Models\Configuracion;
use App\Models\AnotacionesClientePedido;
use Livewire\WithFileUploads;
use App\Models\TipoEmails;
use App\Models\PedidosDocuments;

use App\Models\Stock;
use App\Models\StockEntrante;
use App\Models\StockRegistro;
use App\Models\ProductosMarketing;
use App\Models\ProductosMarketingPedido;
use App\Models\ProductosPedidoPack;
use App\Models\EmpresasTransporte;
use App\Models\ProductosMarketingPedidoPack;
use App\Models\Direcciones;
use App\Models\Albaran;


class EditComponent extends Component
{

    use WithFileUploads;
    use LivewireAlert;
    public $identificador;
    public $porcentaje_descuento = 3; // Nuevo campo para el descuento personalizado
    public $cliente_id;
    public $nombre;
    public $precio;
    public $precioEstimado;
    public $precioSinDescuento;
    public $estado;
    public $estado_old;
    public $direccion_entrega;
    public $provincia_entrega;
    public $localidad_entrega;
    public $cod_postal_entrega;
    public $orden_entrega;
    public $fecha;
    public $observaciones;
    public $tipo_pedido_id;
    public $productos_pedido = [];
    public $productos_pedido_borrar = [];
    public $productos;
    public $clientes;
    public $descuento;
    public $unidades_producto;
    public $addProducto;
    public $producto_seleccionado;
    public $unidades_caja_producto;
    public $unidades_pallet_producto;
    public $precio_crema;
    public $precio_vodka07l;
    public $precio_vodka175l;
    public $precio_vodka3l;
    public $almacen_id;
    public $almacenes;
    public $bloqueado;
    public $porcentaje_bloq;
    public $porcentaje_sincargo;
    public $sinCargo = false;

    public $productoEditar;
    public $productoEditarId;
    public $productoEditarNombre;
    public $productoEditarPrecio;
    public $productoEditarUds;
    public $productoEditarPallets;
    public $productoEditarCajas;

    public $arrProductosEditar = [];
    public $indexPedidoProductoEditar;
    public $fecha_salida;
    public $empresa_transporte;

    public $subtotal;
    public $iva_total;
    public $descuento_total;
    public $npedido_cliente;

    public $gastos_envio;
    public $transporte;
    public $gastos_envio_iva;

    public $registroEmails = [];
    public $emails = [];
    public $emailsSeleccionados = [];
    public $cliente;
    public $emailNuevo;
    public $fecha_entrega;

    public $gestionesPedido = [];
    public $gestion;

    public $anotacionesProximoPedido;
    public $documento;
    public $documentoSubido;
    public $documentoPath;
    public $documentosSubidos = [];

    public $gastos_transporte;
    public $canAccept = true;

    public $productosMarketing = []; // Todos los productos de marketing disponibles
    public $productos_marketing_pedido = []; // Los productos de marketing seleccionados para este pedido
    public $producto_marketing_seleccionado;
    public $precio_producto_marketing = 0.01;
    public $precioEstimadoMarketing = 0;
    public $precioMarketing;

    public $empresasTransporte = [];
    public $numero;

    public $productos_asociados = []; // Nueva propiedad para productos asociados
    public $productos_asociados_marketing = []; // Nueva propiedad para productos asociados marketing

    public $direccionPorDefecto;
    public $localidadPorDefecto;
    public $provinciaPorDefecto;
    public $codPostalPorDefecto;

    public $direcciones = [];
    public $direccion_seleccionada;

    public $productos_sin_stock = [];

    public function getTipo($id){

        $tipo = TipoEmails::find($id);
        if($tipo){
            return $tipo->nombre;
        }else{
            return '';
        }

    }


    // public function addDocumento(){
    //     if($this->documentoSubido !== null){

    //         $this->documentoSubido->storeAs('documentos_justificativos', $this->documentoSubido->hashName() , 'private');
    //         //dd($this->documentoSubido->hashName() );
    //         $this->documentoPath = $this->documentoSubido->hashName();
    //         //eliminar el documento anterior cuyo nombre es $documento
    //         $pedido = Pedido::find($this->identificador);
    //         $documentoAnterior = $pedido->documento;
    //         if($documentoAnterior !== null){
    //             unlink(storage_path('app/private/documentos_justificativos/' . $documentoAnterior));
    //         }

    //     }else{
    //         $this->documentoPath = $this->documento;
    //     }

    //     $pedido = Pedido::find($this->identificador);
    //     $pedido->update([
    //         'documento' => $this->documentoPath
    //     ]);

    //     $this->documento = $this->documentoPath;

    //     $this->documentoSubido = null;

    //     $this->alert('success', '¡Documento subido correctamente!', [
    //         'position' => 'center',
    //         'timer' => 3000,
    //         'toast' => false,
    //     ]);

    // }


    public function ComprobarStockPedido(){
        $stock = true;
        foreach ($this->productos_pedido as $productoPedido) {
            $producto = Productos::find($productoPedido['producto_pedido_id']);
            if($producto->is_pack){
                continue;
            }
            if($producto){
                $hasStock = $this->comprobarStock($producto, $productoPedido['unidades']);
                if (!$hasStock) {
                    $stock = false;
                }
            }
        }

        return $stock;
    }

    public function comprobarStock($producto, $unidades){
        $hasStock = true;
        $stockEntrantes = [];
        $stocks = Stock::where('almacen_id', $this->almacen_id)->get();

        foreach ($stocks as $stock) {

            $stockEntrante = StockEntrante::where('stock_id', $stock->id)->where('producto_id', $producto->id)->first();
            if($stockEntrante){

                $stockEntrantes[] = $stockEntrante;
            }
        }

        $numStockTotal = 0;
        foreach ($stockEntrantes as $stockEntrante) {
            $historialStock = StockRegistro::where('stock_entrante_id', $stockEntrante->id)->sum('cantidad');
            $stock = $stockEntrante->cantidad - $historialStock;
            if($stock < 0){
                $stock = 0;
            }
            $numStockTotal += $stock;
        }

        if($numStockTotal < $unidades){
            $hasStock = false;
            $this->productos_sin_stock[] = [
                'producto' => $producto->nombre,
                'cantidad' => $numStockTotal
            ];
        }
        return $hasStock;
    }



    public function hasFactura(){

        $factura = Facturas::where('pedido_id', $this->identificador)->first();
        if($factura){
            return true;
        }else{
            return false;
        }
    }

    public function pedidoHasAlbaran()
    {
        $albaran = Albaran::where('pedido_id', $this->identificador)->first();
        if ($albaran) {
            return true;
        }
        return false;
    }


    public function addDocumentos()
    {
        if($this->documentosSubidos == null){
            //alert
            $this->alert('error', '¡Cargando documento espere!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }
        if ($this->documentosSubidos !== null) {
            foreach ($this->documentosSubidos as $documento) {
                $documento->storeAs('documentos_pedidos', $documento->hashName(), 'private');
                PedidosDocuments::create([
                    'pedido_id' => $this->identificador,
                    'original_name' => $documento->getClientOriginalName(),
                    'path' => $documento->hashName()
                ]);
            }
        }

        $this->documentosSubidos = null;

        $this->documentos = PedidosDocuments::where('pedido_id', $this->identificador)->get();

        $this->alert('success', '¡Documentos subidos correctamente!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);
    }

    public function eliminarDocumento($id)
    {
        $documento = PedidosDocuments::find($id);
        if ($documento) {
            try {
                unlink(storage_path('app/private/documentos_pedidos/' . $documento->path));
            } catch (\Exception $e) {
                // Manejo de excepciones
            } finally {
                $documento->delete();
                $this->documentos = PedidosDocuments::where('pedido_id', $this->identificador)->get();

                $this->alert('success', '¡Documento eliminado correctamente!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);

                $this->emit('refreshComponent');
            }
        }
    }

    public function descargarDocumento2()
    {
        if($this->documento === null || $this->documento === ''){
            return;
        }

        return response()->download(storage_path('app/private/documentos_justificativos/' . $this->documento),
        'justificativo.pdf'
    );
    }

    public function descargarDocumento($id)
    {
        $documento = PedidosDocuments::find($id);
        if ($documento) {
            return response()->download(storage_path('app/private/documentos_pedidos/' . $documento->path), $documento->original_name);
        }
    }

    public function getNombreProductoMarketing($id){
        $producto = ProductosMarketing::find($id);
        return $producto->nombre ?? 'Producto no encontrado';
    }

    public function mount()
    {
        $pedido = Pedido::find($this->identificador);
        $this->productos = Productos::orderByRaw("CASE WHEN orden IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'orden' al final
        ->orderBy('orden', 'asc')  // Ordenar primero por orden
        ->orderByRaw("CASE WHEN grupo IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'grupo' al final
        ->orderBy('grupo', 'asc')  // Luego ordenar por grupo
        ->orderBy('nombre', 'asc')  // Finalmente, ordenar alfabéticamente por nombre
        ->get();
        $this->clientes = Clients::where('estado', 2)->get();
        $this->cliente_id = ltrim($pedido->cliente_id,0);
        $cliente = Clients::find($this->cliente_id);
        $this->cliente = $cliente;
        $this->estado = $pedido->estado;
        $this->estado_old = $pedido->estado;
        $this->almacenes = Almacen::all();
        $this->almacen_id = $pedido->almacen_id;
        $this->descuento = $pedido->descuento;
        $this->localidad_entrega = $pedido->localidad_entrega;
        $this->provincia_entrega = $pedido->provincia_entrega;
        $this->direccion_entrega = $pedido->direccion_entrega;
        $this->cod_postal_entrega = $pedido->cod_postal_entrega;
        $this->precio_crema = $cliente->precio_crema;
        $this->precio_vodka07l = $cliente->precio_vodka07l;
        $this->precio_vodka175l = $cliente->precio_vodka175l;
        $this->precio_vodka3l = $cliente->precio_vodka3l;
        $this->orden_entrega = $pedido->orden_entrega;
        $this->fecha = $pedido->fecha;
        $this->observaciones = $pedido->observaciones;
        $this->tipo_pedido_id = $pedido->tipo_pedido_id;
        $this->precio = $pedido->precio;
        //dd($this->precio);
        $this->bloqueado = $pedido->bloqueado;
        $this->porcentaje_descuento = $pedido->porcentaje_descuento;
        $this->porcentaje_bloq = is_null($cliente->porcentaje_bloq) ? 10 : $cliente->porcentaje_bloq;
        $this->fecha_salida = $pedido->fecha_salida;
        $this->empresa_transporte = $pedido->empresa_transporte;
        $this->npedido_cliente = $pedido->npedido_cliente;
        $this->gastos_envio = $pedido->gastos_envio;
        $this->gastos_transporte = $pedido->gastos_transporte;
        $this->emails = Emails::where('cliente_id', $cliente->id)->get();
        $this->fecha_entrega = $pedido->fecha_entrega;
        $this->documento = $pedido->documento;
        $this->documentos = PedidosDocuments::where('pedido_id', $this->identificador)->get();
        $this->productosMarketing = ProductosMarketing::all();
        $this->productos_marketing_pedido = ProductosMarketingPedido::where('pedido_id', $this->identificador)->get()->toArray();
        $this->empresasTransporte = EmpresasTransporte::all();
        $this->numero = $pedido->numero ? $pedido->numero : $pedido->id;

        $this->registroEmails = RegistroEmail::where('pedido_id', $this->identificador)->get();
        // if($this->gastos_envio != null && $this->gastos_envio != 0 && is_numeric($this->gastos_envio)){
        //     $this->gastos_envio_iva = $this->gastos_envio * 0.21;
        // }
        if($this->gastos_transporte != null && $this->gastos_transporte != 0 && is_numeric($this->gastos_transporte)){
            $this->gastos_envio_iva = $this->gastos_transporte * 0.21;
        }
        $this->transporte = $pedido->transporte;
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->identificador)->get();
        foreach ($productos as $producto) {
            $productoModel = Productos::find($producto->producto_pedido_id);

            if($productoModel){

                if(isset($productoModel->is_pack) && $productoModel->is_pack){
                    $productosAsociados = json_decode($productoModel->products_id) ?? [];
                    $productosAsociadosMarketing = json_decode($productoModel->products_id_marketing) ?? [];
                    //dd($productoModel);
                    $productosAsociadosPedido = [];
                    $productosAsociadosMarketingPedido = [];

                    foreach($productosAsociados as $productoAsociado){
                        $productoAsociadoModel = ProductosPedidoPack::where('producto_id', $productoAsociado)->where('pedido_id', $this->identificador)->first();
                        if($productoAsociadoModel){
                            $productosAsociadosPedido[] = [
                                'id' => $productoAsociadoModel->producto_id,
                                'nombre' => $productoAsociadoModel->producto->nombre,
                                'unidades' => $productoAsociadoModel->unidades,
                            ];
                        }else{
							//dd($productoAsociado);
							$productoAsociadoModel = Productos::find($productoAsociado);
							//dd($productoAsociadoModel->nombre);
                            $productosAsociadosPedido[] = [
                                'id' => $productoAsociado,
                                'nombre' => $productoAsociadoModel->nombre,
                                'unidades' => 0,
                            ];
							//dd($productoModel);
                        }

                    }

                    foreach($productosAsociadosMarketing as $productoAsociadoMarketing){
                        $productoAsociadoMarketingModel = ProductosMarketingPedidoPack::where('producto_id', $productoAsociadoMarketing)->where('pedido_id', $this->identificador)->first();
                        if($productoAsociadoMarketingModel){
                            $productosAsociadosMarketingPedido[] = [
                                'id' => $productoAsociadoMarketingModel->producto_id,
                                'nombre' => $productoAsociadoMarketingModel->producto->nombre,
                                'unidades' => $productoAsociadoMarketingModel->unidades,
                            ];
                        }else{
                            $productoAsociadoMarketingModel = ProductosMarketing::find($productoAsociadoMarketing);
                            $productosAsociadosMarketingPedido[] = [
                                'id' => $productoAsociadoMarketing,
                                'nombre' => $productoAsociadoMarketingModel->nombre,
                                'unidades' => 0,
                            ];
                        }
                    }

                }
                //dd($productoModel);
                $this->productos_pedido[] = [
                    'id' => $producto->id,
                    'producto_pedido_id' => $producto->producto_pedido_id,
                    'unidades' => $producto->unidades,
                    'precio_ud' => $producto->precio_ud,
                    'precio_total' => $producto->precio_total,
                    'is_pack' => isset($productoModel->is_pack) ? $productoModel->is_pack : false,
                    'productos_asociados' => $productoModel->is_pack ? $productosAsociadosPedido : [],
                    'productos_asociados_marketing' => ($productoModel->is_pack && isset($productoModel->products_id_marketing) ) ? $productosAsociadosMarketingPedido : [],
                    'borrar' => 0,
                ];
            }



        }
        //dd($this->productos_pedido);
        $this->gestionesPedido = GestionPedidos::where('pedido_id', $this->identificador)->where('estado', 'pendiente')->get();
        $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->cliente_id)->where('estado', 'pendiente')->get();
        //alert si hay anotaciones pendientes con botón para cerrar y boton para ver anotaciones
        if (count($this->anotacionesProximoPedido) > 0) {
            $this->alert('info', '¡El cliente tiene anotaciones pendientes!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Cerrar',
                //'showDenyButton' => true,
                //'denyButtonText' => 'Ver anotaciones',
                'onConfirmed' => '',
                //'onDenied' => 'verAnotaciones',
                'timerProgressBar' => true,
            ]);
        }

        if(isset($this->almacen_id) && $this->almacen_id == 6){
            $this->gastos_envio = $pedido->gastos_envio;
            $this->gastos_transporte = $pedido->gastos_transporte;
            $this->transporte = $pedido->transporte;
            $this->subtotal = $pedido->subtotal;
            $this->descuento_total = $pedido->descuento_total;
            $this->iva_total = $pedido->iva_total;
            if($this->precio !== null && $this->precio !== 0){
                $this->iva_total = ($this->precio * 0.21);
            }
        }else{
            $this->setPrecioEstimado();
        }


        $this->canAccept = $this->ComprobarStockPedido();


        if($this->getEstadoNombre() == 'Recibido'  && !$this->canAccept){
            //alerta de stock insuficiente
            $this->alert('info', '¡Stock insuficiente!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Cerrar',
                'timerProgressBar' => true,
            ]);
        }
        $this->setPrecioEstimadoMarketing();
       //
        $this->emit('refreshComponent');

        $this->direcciones = Direcciones::where('cliente_id', $this->cliente_id)->get();
        $this->direccionPorDefecto = $cliente->direccionenvio;
        $this->localidadPorDefecto = $cliente->localidadenvio;
        $this->provinciaPorDefecto = $cliente->provinciaenvio;
        $this->codPostalPorDefecto = $cliente->codPostalenvio;

        $direccionSeleccionada = $this->direcciones->firstWhere('direccion', $this->direccion_entrega);
        // dd($this->direcciones , $this->direccion_entrega);
        $this->direccion_seleccionada = $direccionSeleccionada ? $direccionSeleccionada->id : 'default';


    }


        public function actualizarPrecioTotalMarketing($index)
    {
        $producto = $this->productos_marketing_pedido[$index];
        if (isset($producto['precio_ud']) && isset($producto['unidades'])) {
            $this->productos_marketing_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['unidades'];
        }
        $this->setPrecioEstimadoMarketing();
    }

    public function addProductosMarketing($id)
{
    $producto = ProductosMarketing::find($id);

    if (!$producto) {
        $this->alert('error', 'Producto de marketing no encontrado.', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => false,
            'timerProgressBar' => true,
        ]);
        return;
    }

    $precioUnitario = $this->precio_producto_marketing ?? 0.01;
    $precioTotal = $precioUnitario * $this->unidades_producto;

    $producto_existe = false;
    foreach ($this->productos_marketing_pedido as $productoPedido) {
        if ($productoPedido['producto_marketing_id'] == $id) {
            $producto_existe = true;
            break;
        }
    }

    if ($producto_existe) {
        foreach ($this->productos_marketing_pedido as $index => $productoPedido) {
            if ($productoPedido['producto_marketing_id'] == $id) {
                $this->productos_marketing_pedido[$index]['unidades'] += $this->unidades_producto;
                $this->productos_marketing_pedido[$index]['precio_ud'] = $precioUnitario;
                $this->productos_marketing_pedido[$index]['precio_total'] += $precioTotal;
            }
        }
    } else {
        $this->productos_marketing_pedido[] = [
            'producto_marketing_id' => $id,
            'unidades' => $this->unidades_producto,
            'precio_ud' => $precioUnitario,
            'precio_total' => $precioTotal,
        ];
    }

    $this->producto_marketing_seleccionado = 0;
    $this->unidades_producto = 0;
    $this->precio_producto_marketing = 0.01;
    $this->setPrecioEstimadoMarketing();
    $this->emit('refreshComponent');
}


public function deleteArticuloMarketing($index)
{
    unset($this->productos_marketing_pedido[$index]);
    $this->productos_marketing_pedido = array_values($this->productos_marketing_pedido);
    $this->setPrecioEstimadoMarketing();
    $this->emit('refreshComponent');
}


public function setPrecioEstimadoMarketing()
{
    $this->precioEstimadoMarketing = 0;
    foreach ($this->productos_marketing_pedido as $producto) {
        $this->precioEstimadoMarketing += $producto['precio_total'];
    }
    $this->precioMarketing = number_format($this->precioEstimadoMarketing, 2, '.', '');
}


    public function completarAnotacion($id){
        $gestion = GestionPedidos::find($id);
        $gestion->update([
            'estado' => 'completado'
        ]);
        $this->gestionesPedido = GestionPedidos::where('pedido_id', $this->identificador)->where('estado', 'pendiente')->get();
    }
    public function completarAnotacion2($id){
        $anotacion = AnotacionesClientePedido::find($id);
        $anotacion->update([
            'estado' => 'completado'
        ]);
        $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->cliente_id)->where('estado', 'pendiente')->get();
    }

    public function addAnotacion(){
        if($this->gestion !== null){
            $gestion = GestionPedidos::create([
                'pedido_id' => $this->identificador,
                'gestion' => $this->gestion,
                'estado' => 'pendiente'
            ]);
            $this->gestionesPedido = GestionPedidos::where('pedido_id', $this->identificador)->where('estado', 'pendiente')->get();
            $this->gestion = null;
        }
    }

    public function selectCliente()
    {
        $cliente = Clients::find($this->cliente_id);
        if ($cliente) {
            $this->cliente = $cliente;
            // Almacenar la dirección por defecto del cliente
            $this->direccionPorDefecto = $cliente->direccionenvio;
            $this->localidadPorDefecto = $cliente->localidadenvio;
            $this->provinciaPorDefecto = $cliente->provinciaenvio;
            $this->codPostalPorDefecto = $cliente->codPostalenvio;


            // Inicializar las propiedades de dirección con la dirección por defecto
            $this->direccion_entrega = $this->direccionPorDefecto;
            $this->localidad_entrega = $this->localidadPorDefecto;
            $this->provincia_entrega = $this->provinciaPorDefecto;
            $this->cod_postal_entrega = $this->codPostalPorDefecto;

            $this->direcciones = $cliente->direcciones;

            // Establecer la dirección por defecto
            $this->direccion_seleccionada = 'default';
        }
    }

    public function updatedDireccionSeleccionada($direccionId)
    {
        if ($direccionId !== 'default') {
            $direccion = Direcciones::find($direccionId);
            if ($direccion) {
                $this->direccion_entrega = $direccion->direccion;
                $this->localidad_entrega = $direccion->localidad;
                $this->provincia_entrega = $direccion->provincia;
                $this->cod_postal_entrega = $direccion->codigopostal;
            }
        } else {
            // Restaurar la dirección por defecto del cliente
            $this->direccion_entrega = $this->direccionPorDefecto;
            $this->localidad_entrega = $this->localidadPorDefecto;
            $this->provincia_entrega = $this->provinciaPorDefecto;
            $this->cod_postal_entrega = $this->codPostalPorDefecto;
        }
    }

    public function actualizarPrecioTotal($index)
    {
        $producto = $this->productos_pedido[$index];

        $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['unidades']  ;
        $this->setPrecioEstimado();
    }
    protected $listeners = ['refreshComponent' => '$refresh', 'updateWithoutRestrictions' => 'updateWithoutRestrictions', 'fileUpload' => 'handleFileUpload',
        'addDocumentos'];

    public function handleFileUpload($documentosSubidos)
    {
        $this->documentosSubidos = $documentosSubidos;
    }
    public function render()
    {
        return view('livewire.pedidos.edit-component');
    }

    public function updateWithoutRestrictions(){
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'almacen_id' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento' => 'nullable',
                'porcentaje_descuento'=> 'nullable',
                'bloqueado'=> 'nullable',
                'npedido_cliente' => 'nullable',
                'gastos_envio' => 'nullable',
                'transporte' => 'nullable',
                'fecha_entrega' => 'nullable',
                'fecha_salida' => 'nullable',
                'empresa_transporte' => 'nullable',
                'gastos_transporte' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.requi0red' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        $pedido = Pedido::find($this->identificador);
        $pedido->update($validatedData);
        $pedidosSave = $pedido->update(['estado' => 2]);

        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Aceptado en Almacén',
                'descripcion' => 'El pedido nº ' . $pedido->id.' ha sido aceptado',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);


            $dComercial = User::where('id', 14)->first();
            $dGeneral = User::where('id', 13)->first();
            $administrativo1 = User::where('id', 17)->first();
            $administrativo2 = User::where('id', 18)->first();
            $almacenAlgeciras = User::where('id', 16)->first();
            $almacenCordoba = User::where('id', 15)->first();
            $data = [['type' => 'text', 'text' => $pedido->id]];
            $buttondata = [$pedido->id];

            if(isset($dComercial) && $dComercial->telefono != null){
                $phone = '+34'.$dComercial->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }


            $this->alert('success', '¡Pedido aceptado!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function update()
    {
        $totalUnidades = 0;
        $totalUnidadesSinCargo = 0;
        foreach ($this->productos_pedido as $productoPedido) {
            $totalUnidades += $productoPedido['unidades'];

            if ($productoPedido['precio_ud'] == 0) {
                $totalUnidadesSinCargo += $productoPedido['unidades'];
            }
        }

        if ($totalUnidades > 0) {
            $this->porcentaje_sincargo = ($totalUnidadesSinCargo / $totalUnidades) * 100;
        }

        if($this->porcentaje_sincargo > $this->porcentaje_bloq){
            $this->bloqueado=true;
        }else{$this->bloqueado=false;}
        $total_iva = 0;
        foreach ($this->productos_pedido as $productoPedido) {
            $producto = Productos::find($productoPedido['producto_pedido_id']);
            $precioBaseProducto = $this->obtenerPrecioPorTipo($producto);

            $iva = Iva::find($producto->iva_id);
            if($iva){
                //dd($iva);
                if($this->descuento !== 0 && $this->descuento !== null){
                    $total_iva += (($productoPedido['precio_ud'] * $productoPedido['unidades']) * (1 - ($this->porcentaje_descuento / 100))) * ($iva->iva / 100);
                }else{
                    $total_iva += (($productoPedido['precio_ud'] * $productoPedido['unidades'])) * ($iva->iva / 100);
                }
            }
            // Compara el precio unitario del producto en el pedido con el precio base del cliente
            if ($productoPedido['precio_ud'] != $precioBaseProducto && $productoPedido['precio_ud'] != 0) {
                $this->bloqueado = true;
                break; // Si encuentra una modificación en los precios, no necesita seguir comprobando
            }
        }

        // dd($this->direccion_entrega , $this->provincia_entrega , $this->localidad_entrega , $this->cod_postal_entrega);
        // Validación de datos
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'almacen_id' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento' => 'nullable',
                'porcentaje_descuento'=> 'nullable',
                'bloqueado'=> 'nullable',
                'subtotal' => 'nullable',
                'iva_total' => 'nullable',
                'descuento_total' => 'nullable',
                'npedido_cliente' => 'nullable',
                'gastos_envio' => 'nullable',
                'transporte' => 'nullable',
                'fecha_entrega' => 'nullable',
                'fecha_salida' => 'nullable',
                'empresa_transporte' => 'nullable',
                'gastos_transporte' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        $pedido = Pedido::find($this->identificador);
        // Guardar datos validados
        $pedidosSave = $pedido->update($validatedData);
        ProductosMarketingPedido::where('pedido_id', $this->identificador)->delete();
        foreach ($this->productos_marketing_pedido as $productoMarketing) {
            ProductosMarketingPedido::create([
                'pedido_id' => $this->identificador,
                'producto_marketing_id' => $productoMarketing['producto_marketing_id'],
                'unidades' => $productoMarketing['unidades'],
                'precio_ud' => $productoMarketing['precio_ud'],
                'precio_total' => $productoMarketing['precio_total'],
            ]);
        }

        foreach ($this->productos_pedido as $productos) {

            if (!isset($productos['id'])) {
                DB::table('productos_pedido')->insert([
                    'producto_pedido_id' => $productos['producto_pedido_id'],
                    'pedido_id' => $this->identificador,
                    'unidades' => $productos['unidades'],
                    'precio_ud' => $productos['precio_ud'],
                    'precio_total' => $productos['precio_total']]);

                    if(isset($productos['productos_asociados'])){
                        foreach($productos['productos_asociados'] as $productoAsociado){
                            ProductosPedidoPack::create([
                                'producto_id' => $productoAsociado['id'],
                                'pedido_id' => $this->identificador,
                                'pack_id' => $productos['producto_pedido_id'],
                                'unidades' => $productoAsociado['unidades'],

                            ]);
                        }
                    }
                    if(isset($productos['productos_asociados_marketing'])){
                        foreach($productos['productos_asociados_marketing'] as $productoAsociadoMarketing){
                            ProductosMarketingPedidoPack::create([
                                'producto_id' => $productoAsociadoMarketing,
                                'pedido_id' => $this->identificador,
                                'pack_id' => $productos['producto_pedido_id'],
                                'unidades' => $productoAsociadoMarketing['unidades'],
                            ]);
                        }
                    }
            } else {
                if ($productos['unidades'] > 0) {
                    $unidades_finales = $productos['unidades'] ;
                    DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->update(['unidades' => $unidades_finales, 'precio_ud' => $productos['precio_ud'], 'precio_total' => $productos['precio_total']]);
                    if(isset($productos['productos_asociados'])){
                        //dd($productos['productos_asociados']);
                        foreach($productos['productos_asociados'] as $productoAsociado){
                           $productoPack = ProductosPedidoPack::where('producto_id', $productoAsociado['id'])->where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->first();
                           if($productoPack){
                            //dd($productoPack , $productoAsociado);
                            $productoPack->update(['unidades' => $productoAsociado['unidades']]);
                           }
                        }
                    }

                    if(isset($productos['productos_asociados_marketing'])){
                        foreach($productos['productos_asociados_marketing'] as $productoAsociadoMarketing){
                            $productoPackMarketing = ProductosMarketingPedidoPack::where('producto_id', $productoAsociadoMarketing)->where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->first();
                            if($productoPackMarketing){
                                $productoPackMarketing->update(['unidades' => $productoAsociadoMarketing['unidades']]);
                            }
                        }
                    }
                    //dd(ProductosMarketingPedidoPack::where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->get());
                } else {
                    DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->update(['precio_ud' => $productos['precio_ud']]);
                    if(isset($productos['productos_asociados'])){
                        foreach($productos['productos_asociados'] as $productoAsociado){
                            ProductosPedidoPack::where('producto_id', $productoAsociado['id'])->where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->delete();
                        }
                    }

                    if(isset($productos['productos_asociados_marketing'])){
                        foreach($productos['productos_asociados_marketing'] as $productoAsociadoMarketing){
                            ProductosMarketingPedidoPack::where('producto_id', $productoAsociadoMarketing)->where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->delete();
                        }
                    }
                }
            }
        }
        foreach ($this->productos_pedido_borrar as $productos) {
            if (isset($productos['id'])) {
                DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->delete();
                if(isset($productos['productos_asociados'])){
                    foreach($productos['productos_asociados'] as $productoAsociado){
                        ProductosPedidoPack::where('producto_id', $productoAsociado['id'])->where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->delete();
                    }
                }

                if(isset($productos['productos_asociados_marketing'])){
                    foreach($productos['productos_asociados_marketing'] as $productoAsociadoMarketing){
                        ProductosMarketingPedidoPack::where('producto_id', $productoAsociadoMarketing)->where('pack_id', $productos['producto_pedido_id'])->where('pedido_id', $this->identificador)->delete();
                    }
                }
            }
        }
       // event(new \App\Events\LogEvent(Auth::user(), 4, $pedido->id));

        // Alertas de guardado exitoso
        if ($pedidosSave) {

            //Update factura relacionada si existe
            $factura = Facturas::where('pedido_id', $this->identificador)->first();
            if($factura){
                $factura->update(
                    ['precio' => $this->precio,
                    'cliente_id' => $this->cliente_id,
                    'gastos_envio' => $this->gastos_envio,
                    'gasos_transporte' => $this->gastos_transporte,
                    'transporte' => $this->transporte,
                    'descuento' => $this->descuento,
                    'porcentaje_descuento' => $this->porcentaje_descuento,
                    ]
                );
                $this->calcularTotales($factura);

                //dd($factura);
            }

            if( $this->bloqueado && $this->estado == 1){
                Alertas::create([
                    'user_id' => 13,
                    'stage' => 2,
                    'titulo' => 'Pedido Bloqueado: Pendiente de Aprobación',
                    'descripcion' => 'El pedido nº' . $pedido->id .' esta a la espera de aprobación',
                    'referencia_id' => $pedido->id,
                    'leida' => null,
                ]);


                $dComercial = User::where('id', 14)->first();
                $dGeneral = User::where('id', 13)->first();
                $administrativo1 = User::where('id', 17)->first();
                $administrativo2 = User::where('id', 18)->first();
                $almacenAlgeciras = User::where('id', 16)->first();
                $almacenCordoba = User::where('id', 15)->first();
                $data = [['type' => 'text', 'text' => $pedido->id]];
                $buttondata = [$pedido->id];

                if(isset($dComercial) && $dComercial->telefono != null){
                    $phone = '+34'.$dComercial->telefono;
                    enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
                }

                if(isset($dGeneral) && $dGeneral->telefono != null){
                    $phone = '+34'.$dGeneral->telefono;
                    enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
                }

                if(isset($administrativo1) && $administrativo1->telefono != null){
                    $phone = '+34'.$administrativo1->telefono;
                    enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
                }

                if(isset($administrativo2) && $administrativo2->telefono != null){
                    $phone = '+34'.$administrativo2->telefono;
                    enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
                }

                if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                    $phone = '+34'.$almacenAlgeciras->telefono;
                    enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
                }

                if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
                    $phone = '+34'.$almacenCordoba->telefono;
                    enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
                }

            }

            $this->alert('success', '¡Pedido registrado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'update',
            'alertaGuardar',
            'confirmDelete',
            'destroy',
            'imprimirPedido',
            'aceptarPedido',
            'rechazarPedido',
            'alertaAlmacen',
            'updateAlmacen',
            'checkLote',
            'updateWithoutRestrictions'
        ];
    }

    public function calcularTotales($factura){
        $iva= 0;
        $total = 0;
        //si hay pedido id
        if(isset($factura) && isset($factura->pedido_id) && $factura->pedido_id != null){
            //coger el precio del pedido y sumarle el iva
            $total = $factura->precio + $this->iva_total;
            $iva = $this->iva_total;
            $factura->iva = $iva;
            $factura->iva_total_pedido = $iva;
            $factura->descuento_total_pedido = $this->descuento_total;
            $factura->subtotal_pedido = $this->subtotal;
            $factura->total = $total;
            $factura->save();

        }else{
            if(isset($factura) && isset($factura->precio) && $factura->precio != null){

                $total = $factura->precio;
                $iva = (($factura->precio * 21) / 100);
                if($factura->descuento){
                    $total = $total - (($total * $factura->descuento) / 100);
                }

                //total es total + iva
                $total = $total + $iva;

                $factura->iva = $iva;
                $factura->iva_total_pedido = $iva;
                $factura->descuento_total_pedido = $this->descuento_total;
                $factura->subtotal_pedido = $this->subtotal;
                $factura->total = $total;
                $factura->save();

            }
        }

    }

    public function updateAlmacen()
    {
        $this->estado = 4;
        // Validación de datos
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        $pedido = Pedido::find($this->identificador);
        // Guardar datos validados
        $pedidosSave = $pedido->update($validatedData);

        foreach ($this->productos_pedido as $productos) {
            if (!isset($productos['id'])) {
                DB::table('productos_pedido')->insert(['producto_pedido_id' => $productos['producto_pedido_id'], 'pedido_id' => $this->identificador, 'unidades' => $productos['unidades']]);
                /*$producto_stock = ProductoLote::find($productos['producto_pedido_id']);
                $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
                $producto_stock->update(['cantidad_actual' => $cantidad_actual]);*/
            } else {
                if ($productos['unidades'] > 0) {
                    $unidades_finales = $productos['unidades'];
                    DB::table('productos_pedido')->find($productos['id'])->update(['unidades' => $unidades_finales]);
                   /* $producto_stock = ProductoLote::find($productos['producto_pedido_id']);
                    $cantidad_actual = $producto_stock->cantidad_actual - $productos['unidades'];
                    $producto_stock->update(['cantidad_actual' => $cantidad_actual]);*/
                }
            }
        }
       // event(new \App\Events\LogEvent(Auth::user(), 4, $pedido->id));

        // Alertas de guardado exitoso
        if ($pedidosSave) {
            $this->alert('success', '¡Pedido enviado a Almacén!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function alertaGuardar()
    {

        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'timer' => null,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }

    public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el Pedido? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);

    }

    public function aceptarPedido()
    {

        $cliente = Clients::find($this->cliente_id);

        //Traer todas las facturas del cliente
        $facturas = Facturas::where('cliente_id', $cliente->id)->where('estado', '!=', 'Pagado')->get();
        //sum total de facturas
        $totalFacturas = 0;
        foreach ($facturas as $factura) {
            $totalFacturas += $factura->total;
        }

        $pedido = Pedido::find($this->identificador);

        //confirming
        $confirmig = $cliente->credito - $totalFacturas - $this->precio;
        //dd($totalFacturas , $this->precio, $confirmig, $cliente->credito);

        if($this->porcentaje_descuento > $this->porcentaje_bloq || $cliente->credito !== null && $confirmig < 0){
            $this->bloqueado=true;
            if($this->porcentaje_descuento > $this->porcentaje_bloq){
                $bloqueadopor = '1';
            }else{
                $bloqueadopor = '2';
            }

        }else{$this->bloqueado=false;}

        //dd($this->bloqueado);
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'almacen_id' => 'nullable',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento' => 'nullable',
                'porcentaje_descuento'=> 'nullable',
                'bloqueado'=> 'nullable',
                'npedido_cliente' => 'nullable',
                'fecha_entrega' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.requi0red' => 'El precio del pedido es obligatorio.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );
        $pedido = Pedido::find($this->identificador);
        $pedido->update($validatedData);
        if($this->bloqueado){
            if($bloqueadopor == '1'){
                return $this->alert('warning', 'El pedido ha sido bloqueado por superar el porcentaje de descuento permitido. ¿Desea aceptar el pedido? ', [
                    'position' => 'center',
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'updateWithoutRestrictions',
                    'confirmButtonText' => 'Sí',
                    'showDenyButton' => true,
                    'denyButtonText' => 'No',
                    'timerProgressBar' => true,
                    'timer' => null,
                ]);
            }else{
                return $this->alert('warning', 'El pedido ha sido bloqueado por superar el crédito del cliente. ¿Desea aceptar el pedido? ', [
                    'position' => 'center',
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'updateWithoutRestrictions',
                    'confirmButtonText' => 'Sí',
                    'showDenyButton' => true,
                    'denyButtonText' => 'No',
                    'timerProgressBar' => true,
                    'timer' => null,
                ]);

            }
        }
        $pedidosSave = $pedido->update(['estado' => 2]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Aceptado en Almacén',
                'descripcion' => 'El pedido nº ' . $pedido->id.' ha sido aceptado',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);


            $dComercial = User::where('id', 14)->first();
            $dGeneral = User::where('id', 13)->first();
            $administrativo1 = User::where('id', 17)->first();
            $administrativo2 = User::where('id', 18)->first();
            $almacenAlgeciras = User::where('id', 16)->first();
            $almacenCordoba = User::where('id', 15)->first();
            $data = [['type' => 'text', 'text' => $pedido->id]];
            $buttondata = [$pedido->id];

            if(isset($dComercial) && $dComercial->telefono != null){
                $phone = '+34'.$dComercial->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            $this->alert('success', '¡Pedido aceptado!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }


    public function addAdjunto(){

    }

    public function rechazarPedido()
    {
        $pedido = Pedido::find($this->identificador);
        $pedidosSave = $pedido->update(['estado' => 7]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Rechazado ',
                'descripcion' => 'El pedido nº ' . $pedido->id . ' ha sido rechazado',
                'referencia_id' => $pedido->id,
                'leida' => null,
            ]);


            $dComercial = User::where('id', 14)->first();
            $dGeneral = User::where('id', 13)->first();
            $administrativo1 = User::where('id', 17)->first();
            $administrativo2 = User::where('id', 18)->first();
            $almacenAlgeciras = User::where('id', 16)->first();
            $almacenCordoba = User::where('id', 15)->first();

            $data = [['type' => 'text', 'text' => $pedido->id]];
            $buttondata = [$pedido->id];

            if(isset($dComercial) && $dComercial->telefono != null){
                $phone = '+34'.$dComercial->telefono;
                enviarMensajeWhatsApp('pedido_rechazado', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_rechazado', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_rechazado', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_rechazado', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_rechazado', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_rechazado', $data, $buttondata, $phone);
            }

            $this->alert('success', '¡Pedido rechazado!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido enviar el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function alertaAceptar()
    {
        if(isset($this->almacen_id) && $this->almacen_id != 0){
            $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
                'position' => 'center',
                'toast' => false,
                'timer' => null,
                'showConfirmButton' => true,
                'onConfirmed' => 'aceptarPedido',
                'confirmButtonText' => 'Sí',
                'showDenyButton' => true,
                'denyButtonText' => 'No',
                'timerProgressBar' => false,
            ]);
        }else{
            $this->alert('info', 'Debe seleccionar un almacén antes de proceder.', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        }



    }

    public function alertaRechazar()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'timer' => null,
            'showConfirmButton' => true,
            'onConfirmed' => 'rechazarPedido',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }

    public function updatePallet()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = $this->unidades_pallet_producto * $producto->cajas_por_pallet;
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;
    }
    public function updateCaja()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;
    }
    public function updateUnidad()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = floor($this->unidades_producto / $producto->unidades_por_caja);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
    }
    public function alertaAlmacen()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de proceder.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'updateAlmacen',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }
    public function getUnidadesTabla($id)
    {
        $producto = Productos::find($this->productos_pedido[$id]['producto_pedido_id']);
        // if (isset($this->productos_pedido[$id]['unidades_old'])) {
        //     $uds_total = $this->productos_pedido[$id]['unidades_old'] + $this->productos_pedido[$id]['unidades'];
        //     $cajas = ($uds_total / $producto->unidades_por_caja);
        //     $pallets = floor($cajas / $producto->cajas_por_pallet);
        //     $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
        //     $unidades = '';
        //     if ($cajas_sobrantes > 0) {
        //         $unidades = $uds_total . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
        //     } else {
        //         $unidades = $uds_total . ' unidades (' . $pallets . ' pallets)';
        //     }
        // } else {

            if(isset($producto)){
                $cajas = ($this->productos_pedido[$id]['unidades'] / $producto->unidades_por_caja);
                $pallets = floor($cajas / $producto->cajas_por_pallet);
                $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
                $unidades = '';
                if ($cajas_sobrantes > 0) {
                    $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
                } else {
                    $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets)';
                }
            }else{
                $unidades = 0;
            }

        // }

        return $unidades;
    }

    public function deleteArticulo($id)
    {

        $this->productos_pedido_borrar[] = $this->productos_pedido[$id];
        unset($this->productos_pedido[$id]);
        $this->productos_pedido = array_values($this->productos_pedido);
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }
    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first();
        if(!isset($nombre_producto)){
            $nombre_producto = 'producto no encontrado';
            return $nombre_producto;
        }else{
            return $nombre_producto->nombre;
        }

    }


    public function getNombreTablaMarketing($id)
    {
        $producto = ProductosMarketing::find($id);
        if(!isset($producto)){
            $producto = 'producto no encontrado';
            return $producto;
        }else{
            return $producto->nombre;
        }
    }

    public function editProductos($id){

        // $this->arrProductosEditar = [];

        // //añadir al array el productoEditar junto a las unidades correspondientes
        // $this->arrProductosEditar[$this->productoEditarId][] = [
        //     'producto_pedido_id' => $this->productoEditarId,
        //     'unidades' => $this->unidades_producto,
        //     'precio_ud' => $this->productoEditarPrecio,
        //     'precio_total' => $this->productoEditarUds * $this->productoEditarPrecio
        // ];

        //añadir a la tabla de productos_pedido, editando los valores si ya existe

        foreach ($this->productos_pedido as $index => $productoPedido) {
            if ($index == $id) {
                $this->productos_pedido[$index]['unidades'] = $this->unidades_producto;
                if($this->sinCargo == true){
                    $this->productos_pedido[$index]['precio_ud'] = 0;
                    $this->productos_pedido[$index]['precio_total'] = 0;
                }else{
                    $this->productos_pedido[$index]['precio_ud'] = $this->productoEditarPrecio;
                    $this->productos_pedido[$index]['precio_total'] = $this->unidades_producto * $this->productoEditarPrecio;
                }
            }
        }
        $this->setPrecioEstimado();
        $this->sinCargo = false;

        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->unidades_caja_producto = 0;
        $this->unidades_pallet_producto = 0;
        $this->emit('refreshComponent');



    }


    public function addProductos($id)
    {
        $producto = Productos::find($id);
        if (!$producto) {
            $this->alert('error', 'Producto no encontrado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }

        // Verificar si el producto es un pack
        if ($producto->is_pack) {
            $productosAsociados = json_decode($producto->products_id ?? '[]'); // Asegúrate de que sea un array
            $productosAsociadosMarketing = json_decode($producto->products_marketing_id ?? '[]'); // Asegúrate de que sea un array
           $productos_asociados = [];
           $productos_asociados_marketing = [];

            foreach ($productosAsociados as $productoAsociado) {
                $productoAsociadoModel = Productos::find($productoAsociado);
                if($productoAsociadoModel){
                    $productos_asociados[] = [
                        'id' => $productoAsociadoModel->id,
                        'nombre' => $productoAsociadoModel->nombre,
                        'unidades' => 0, // Inicialmente 0, el usuario puede ajustar después
                    ];
                }
            }

            foreach($productosAsociadosMarketing as $productoAsociadoMarketing){
                $productoAsociadoMarketingModel = ProductosMarketing::find($productoAsociadoMarketing);
                if($productoAsociadoMarketingModel){
                    $productos_asociados_marketing[] = [
                        'id' => $productoAsociadoMarketingModel->id,
                        'nombre' => $productoAsociadoMarketingModel->nombre,
                        'unidades' => 0, // Inicialmente 0, el usuario puede ajustar después
                    ];
                }
            }

            $this->productos_pedido[] = [
                'producto_pedido_id' => $producto->id,
                'unidades' => $this->unidades_producto,
                'precio_ud' => $this->productoEditarPrecio,
                'precio_total' => $this->unidades_producto * $this->productoEditarPrecio,
                'is_pack' => true,
                'productos_asociados' => $productos_asociados,
                'productos_asociados_marketing' => $productos_asociados_marketing,
            ];

            //dd($this->productos_pedido);
        } else {
            // Lógica existente para productos que no son pack
            $this->productos_pedido[] = [
                'producto_pedido_id' => $producto->id,
                'unidades' => $this->unidades_producto,
                'precio_ud' => $this->productoEditarPrecio,
                'precio_total' => $this->unidades_producto * $this->productoEditarPrecio,
            ];
        }

        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }

    private function agregarProductoAlPedido($producto)
    {
        $precioUnitario = $this->obtenerPrecioPorTipo($producto);
        $precioTotal = $precioUnitario * $this->unidades_producto;

        $producto_existe = false;
        $producto_existe_sincargo =false;
        foreach ($this->productos_pedido as $productoPedido) {
            if ($productoPedido['producto_pedido_id'] == $producto->id) {
                if ($productoPedido['precio_ud'] !== 0) {
                    $producto_existe = true;
                    break;
                }else{
                    $producto_existe_sincargo = true;
                }
            }
        }
		if($this->sinCargo == true){
            if ($producto_existe_sincargo) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $producto->id) {
                        if ($productoPedido['precio_ud'] == 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
			} else {
			$this->productos_pedido[] = [
                'producto_pedido_id' => $producto->id,
                'unidades' => $this->unidades_producto,
                'precio_ud' => 0,
                'precio_total' => 0
            ];}

		} else{


			if ($producto_existe) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $producto->id) {
                        if ($productoPedido['precio_ud'] !== 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
				$precioUnitario = $this->productos_pedido[$key]['precio_ud'];
                $precioTotal = $precioUnitario * $this->unidades_producto;
				$this->productos_pedido[$key]['precio_total'] += $precioTotal;
			} else {
				$this->productos_pedido[] = [
					'producto_pedido_id' => $producto->id,
					'unidades' => $this->unidades_producto,
					'precio_ud' => $precioUnitario,
					'precio_total' => $precioTotal
				];
			}

		}
    }

    private function obtenerPrecioPorTipo($producto)
    {
        if(isset($producto)){
            $tipoPrecio = $producto->tipo_precio;
            switch ($tipoPrecio) {
                case 1:
                    return $this->precio_crema;
                case 2:
                    return $this->precio_vodka07l;
                case 3:
                    return $this->precio_vodka175l;
                case 4:
                    return $this->precio_vodka3l;
                case 5:
                    return $producto->precio;
                default:
                    return 0;
            }
        }else{
            return 0;
        }
    }

    public function selectProduct($id, $precio, $unidades, $idIndex){
        //selecciona el producto para editarlo
        $this->productoEditar = Productos::find($id);
        $this->productoEditarId = $id;
        $this->productoEditarNombre = $this->productoEditar->nombre;
        $this->productoEditarUds = $unidades;
        $this->productoEditarPrecio = $precio;
        $this->productoEditarPallets = floor($unidades / $this->productoEditar->unidades_por_caja / $this->productoEditar->cajas_por_pallet);
        $this->productoEditarCajas = floor($unidades / $this->productoEditar->unidades_por_caja);

        $this->producto_seleccionado = $this->productoEditarId;
        $this->unidades_producto = $this->productoEditarUds;
        $this->unidades_pallet_producto = $this->productoEditarPallets;
        $this->unidades_caja_producto = $this->productoEditarCajas;

        $this->indexPedidoProductoEditar = $idIndex;

        if($this->sinCargo == true){

            $this->productoEditarPrecio = 0;
        }

    }

    public function setPrecioEstimado()
    {
        $this->precioEstimado = 0;
        if($this->gastos_envio != 0 && $this->gastos_envio != null && is_numeric($this->gastos_envio)){
            //dd($this->gastos_envio);
            // $this->precioEstimado = $this->gastos_envio;
            //$this->gastos_envio_iva = $this->gastos_envio * 0.21;
        }

        if($this->gastos_transporte != 0 && $this->gastos_transporte != null && is_numeric($this->gastos_transporte)){
            $this->precioEstimado += $this->gastos_transporte;
            $this->gastos_envio_iva = $this->gastos_transporte * 0.21;
        }


        foreach ($this->productos_pedido as $producto) {
            $this->precioEstimado += $producto['precio_total'];
        }
        $this->precioSinDescuento = $this->precioEstimado;
        // Verificar si el descuento está activado
        if ($this->descuento) {
            // Calcular el 3% de descuento del precio total
            //$descuento = $this->descuento_personalizado ?? ($this->precioEstimado * 0.03);
            $descuento = $this->precioEstimado * ($this->porcentaje_descuento / 100);

            // Aplicar el descuento al precio total
            $this->precioEstimado -= $descuento;
            $this->descuento_total = $this->precioSinDescuento - $this->precioEstimado;
            $this->descuento_total = number_format($this->descuento_total, 2, '.', '');

        }else{
            $this->descuento_total = 0;
        }



        // Asignar el precio final
        $this->precio = number_format($this->precioEstimado, 2, '.', '');
        $this->subtotal = $this->precioSinDescuento;
        //calcular iva de los productos
        $total_iva  = 0;
        foreach ($this->productos_pedido as $productoPedido) {
            $producto = Productos::find($productoPedido['producto_pedido_id']);
            $precioBaseProducto = $this->obtenerPrecioPorTipo($producto);
            //ver que iva tiene el producto
            if(isset($producto)){
                $iva = Iva::find($producto->iva_id);
                if($iva){
                    //dd($iva);
                    if($this->descuento == 1){
                        //dd($this->descuento);
                        $total_iva += (($productoPedido['precio_ud'] * $productoPedido['unidades']) * (1 - ($this->porcentaje_descuento / 100))) * ($iva->iva / 100);
                    }else{
                        $total_iva += (($productoPedido['precio_ud'] * $productoPedido['unidades'])) * ($iva->iva / 100);
                    }
                }
            }

        }

        // if($this->gastos_envio != 0 && $this->gastos_envio != null && is_numeric($this->gastos_envio)){
        //     $total_iva += $this->gastos_envio_iva;
        // }

        if($this->gastos_transporte != 0 && $this->gastos_transporte != null && is_numeric($this->gastos_transporte)){
            $total_iva += $this->gastos_envio_iva;
        }

        $this->iva_total = $total_iva;
    }



    public function getProductoNombre()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->nombre != null) {
            return $producto->nombre;
        }
    }
    public function getProductoPrecio()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->precio != null) {
            return $producto->precio . "€ (Sin IVA)";
        }
    }
    public function getProductoPrecioIVA()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null && $producto->precio != null) {
            return ($producto->precio + ($producto->precio * ($producto->iva / 100))) . "€ (Con IVA)";
        }
    }
    public function getProductoUds()
    {
        if ($this->lote_seleccionado != null) {
            $producto = ProductoLote::find($this->lote_seleccionado);
            if ($producto != null && $producto->cantidad_actual != null) {
                foreach ($this->productos_pedido as $productos) {
                    if ($productos['producto_pedido_id'] == $this->lote_seleccionado) {
                        return ($producto->cantidad_actual - $this->unidades_producto - $productos["unidades"]);
                    }
                }
                return ($producto->cantidad_actual - $this->unidades_producto);
            }
        }
    }
    public function getProductoImg()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null) {
            return asset('storage/photos/' . $producto->foto_ruta);
        }

        $this->emit('refreshComponent');
    }


    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('pedidos.index');
    }

    public function getEstadoNombre()
    {
        return PedidosStatus::firstWhere('id', $this->estado)->status;
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

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user) {
            return $user->name;
        } else {
            return '';
        }
    }


    public function confirmDelete()
    {
        $pedidoId = $this->identificador;

        // Primero, elimina todos los productos_pedido asociados con este pedido
        DB::table('productos_pedido')->where('pedido_id', $pedidoId)->delete();

        // Luego, elimina el pedido
        $pedido = Pedido::find($pedidoId);
        if ($pedido) {
           // event(new \App\Events\LogEvent(Auth::user(), 10, $pedido->id));
            $pedido->delete();
        }

        return redirect()->route('pedidos.index');

    }
    public function imprimirPedido()
    {
    $pedido = Pedido::find($this->identificador);
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
            ];
        }
    }
    $iva= true;
    // if($cliente->delegacion){

    //     if ($cliente->delegacion && in_array($cliente->delegacion['id'], [15, 14, 13, 7])) {
    //         $iva = false;
    //     }
    // }

    $delegacionNombre = $cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene
    if($delegacionNombre == '07 CANARIAS' || $delegacionNombre == '13 GIBRALTAR' || $delegacionNombre == '14 CEUTA' || $delegacionNombre == '15 MELILLA' || $delegacionNombre == '01.1 ESTE – SUR EXTERIOR' || $delegacionNombre == '08 OESTE - INSULAR'
    ){
        $iva = false;
    }

    $configuracion = Configuracion::first();
    $datos = [
        'conIva' => $iva,
        'pedido' => $pedido,
        'cliente' => $cliente,
        'localidad_entrega' => $pedido->localidad_entrega,
        'direccion_entrega' => $pedido->direccion_entrega,
        'cod_postal_entrega' => $pedido->cod_postal_entrega,
        'provincia_entrega' => $pedido->provincia_entrega,
        'fecha' => $pedido->fecha,
        'observaciones' => $pedido->observaciones,
        'precio' => $pedido->precio,
        'descuento' => $pedido->descuento,
        'productos' => $productos,
        'configuracion' => $configuracion,
    ];

    $pdf = PDF::loadView('livewire.pedidos.pdf-component', $datos)->setPaper('a4', 'vertical');
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
            'Pedidos@serlobo.com'
        ];
        if($this->almacen_id == 2){
            //push emailsDireccion
            $emailsDireccion[] = 'Almacen.cordoba@serlobo.com';

        }



        if(count($this->emailsSeleccionados) > 0){
            if($this->emailNuevo != null){
                array_push($this->emailsSeleccionados, $this->emailNuevo);
            }

            Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));
            // Mail::to('ivan.mayol@hawkins.es')->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));

            foreach($this->emailsSeleccionados as $email){
                $registroEmail = new RegistroEmail();
                $registroEmail->factura_id =null;
                $registroEmail->pedido_id = $pedido->id;
                $registroEmail->cliente_id = $pedido->cliente_id;
                $registroEmail->email = $email;
                $registroEmail->user_id = Auth::user()->id;
                $registroEmail->tipo_id = 2;
                $registroEmail->save();
            }

            if($this->emailNuevo != null){

                $registroEmail = new RegistroEmail();
                $registroEmail->factura_id =null;
                $registroEmail->pedido_id = $pedido->id;
                $registroEmail->cliente_id = $pedido->cliente_id;
                $registroEmail->email = $this->emailNuevo;
                $registroEmail->user_id = Auth::user()->id;
                $registroEmail->tipo_id = 2;
                $registroEmail->save();
            }

        }else{
            if($this->emailNuevo != null){
                Mail::to($this->emailNuevo)->cc($this->emailNuevo)->bcc($emailsDireccion)->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos,  $iva));
                // Mail::to('ivan.mayol@hawkins.es')->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));

            }else{
                Mail::to($cliente->email)->bcc($emailsDireccion)->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos,  $iva));
                // Mail::to('ivan.mayol@hawkins.es')->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));

            }

            $registroEmail = new RegistroEmail();
            $registroEmail->factura_id = null;
            $registroEmail->pedido_id = $pedido->id;
            $registroEmail->cliente_id = $pedido->cliente_id;
            $registroEmail->email = $cliente->email;
            $registroEmail->user_id = Auth::user()->id;
            $registroEmail->tipo_id = 2;
            $registroEmail->save();

            if($this->emailNuevo != null){
                $registroEmail = new RegistroEmail();
                $registroEmail->factura_id = null;
                $registroEmail->pedido_id = $pedido->id;
                $registroEmail->cliente_id = $pedido->cliente_id;
                $registroEmail->email = $this->emailNuevo;
                $registroEmail->user_id = Auth::user()->id;
                $registroEmail->tipo_id = 2;
                $registroEmail->save();
            }

        }
        $this->alert('success', '¡Pedido enviado correctamente!', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
            'timerProgressBar' => false,
        ]);
    }catch(\Exception $e){
        //mostrarme el error
        //dd($e);
        $this->alert('error', '¡No se ha podido enviar el pedido!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);
    }

    /*--return response()->streamDownload(
        fn () => print($pdf),
        "pedido_{$pedido->id}.pdf"
    );*/
}

    public function save()
    {
        // Lógica para guardar el pedido
        $pedido = Pedido::find($this->identificador);
        $pedido->update([
            // ... otros campos ...
        ]);

        // Guardar productos asociados
        foreach ($this->productos_asociados as $productoAsociado) {
            ProductosPedidoPack::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $productoAsociado['id'],
                'unidades' => $productoAsociado['unidades'],
            ]);
        }

        foreach ($this->productos_asociados_marketing as $productoAsociadoMarketing) {
            ProductosMarketingPedidoPack::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $productoAsociadoMarketing['id'],
                'unidades' => $productoAsociadoMarketing['unidades'],
            ]);
        }

        // Lógica existente para guardar productos
        foreach ($this->productos_pedido as $productoPedido) {
            // ... lógica existente ...
        }

        $this->alert('success', '¡Pedido actualizado correctamente!', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
        ]);
    }

}
