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
use App\Models\AnotacionesClientePedido;
use Livewire\WithFileUploads;
use App\Models\TipoEmails;

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


    public function getTipo($id){

        $tipo = TipoEmails::find($id);
        if($tipo){
            return $tipo->nombre;
        }else{
            return '';
        }

    }


    public function addDocumento(){
        if($this->documentoSubido !== null){
                
            $this->documentoSubido->storeAs('documentos_justificativos', $this->documentoSubido->hashName() , 'private');
            //dd($this->documentoSubido->hashName() );
            $this->documentoPath = $this->documentoSubido->hashName();
            //eliminar el documento anterior cuyo nombre es $documento
            $pedido = Pedido::find($this->identificador);
            $documentoAnterior = $pedido->documento;
            if($documentoAnterior !== null){
                unlink(storage_path('app/private/documentos_justificativos/' . $documentoAnterior));
            }

        }else{
            $this->documentoPath = $this->documento;
        }

        $pedido = Pedido::find($this->identificador);
        $pedido->update([
            'documento' => $this->documentoPath
        ]);

        $this->documento = $this->documentoPath;

        $this->documentoSubido = null;

        $this->alert('success', '¡Documento subido correctamente!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);

    }

    public function descargarDocumento()
    {
        if($this->documento === null || $this->documento === ''){
            return;
        }

        return response()->download(storage_path('app/private/documentos_justificativos/' . $this->documento),
        'justificativo.pdf'
    );
    }

    public function mount()
    {
        $pedido = Pedido::find($this->identificador);
        $this->productos = Productos::all();
        $this->clientes = Clients::where('estado', 2)->get();
        $this->cliente_id = ltrim($pedido->cliente_id,0);
        $cliente = Clients::find($this->cliente_id);
        $this->cliente = $cliente;
        $this->estado = $pedido->estado;
        $this->estado_old = $pedido->estado;
        $this->almacenes = Almacen::all();
        $this->almacen_id = $pedido->almacen_id;
        $this->descuento = $pedido->descuento;
        $this->localidad_entrega = $cliente->localidadenvio;
        $this->provincia_entrega = $cliente->provinciaenvio;
        $this->direccion_entrega = $cliente->direccionenvio;
        $this->cod_postal_entrega = $cliente->codPostalenvio;
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
        $this->emails = Emails::where('cliente_id', $cliente->id)->get();
        $this->fecha_entrega = $pedido->fecha_entrega;
        $this->documento = $pedido->documento;

        $this->registroEmails = RegistroEmail::where('pedido_id', $this->identificador)->get();
        if($this->gastos_envio != null && $this->gastos_envio != 0 && is_numeric($this->gastos_envio)){
            $this->gastos_envio_iva = $this->gastos_envio * 0.21;
        }
        $this->transporte = $pedido->transporte;
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->identificador)->get();
        foreach ($productos as $producto) {
            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_pedido_id' => $producto->producto_pedido_id,
                'unidades' => $producto->unidades,
                'precio_ud' => $producto->precio_ud,
                'precio_total' => $producto->precio_total,
                'borrar' => 0,
            ];
        }
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

       
       // 
        $this->emit('refreshComponent');

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
        $this->localidad_entrega = $cliente->localidad;
        $this->provincia_entrega = $cliente->provincia;
        $this->direccion_entrega = $cliente->direccion;
        $this->cod_postal_entrega = $cliente->cod_postal;
        $this->precio_crema = $cliente->precio_crema;
        $this->precio_vodka07l = $cliente->precio_vodka07l;
        $this->precio_vodka175l = $cliente->precio_vodka175l;
        $this->precio_vodka3l = $cliente->precio_vodka3l;
    }

    public function actualizarPrecioTotal($index)
    {
        $producto = $this->productos_pedido[$index];

        $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['unidades']  ;
        $this->setPrecioEstimado();
    }
    protected $listeners = ['refreshComponent' => '$refresh', 'updateWithoutRestrictions' => 'updateWithoutRestrictions'];


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
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
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
                DB::table('productos_pedido')->insert([
                    'producto_pedido_id' => $productos['producto_pedido_id'],
                    'pedido_id' => $this->identificador,
                    'unidades' => $productos['unidades'],
                    'precio_ud' => $productos['precio_ud'],
                    'precio_total' => $productos['precio_total']]);
            } else {
                if ($productos['unidades'] > 0) {
                    $unidades_finales = $productos['unidades'] ;
                    DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->update(['unidades' => $unidades_finales, 'precio_ud' => $productos['precio_ud'], 'precio_total' => $productos['precio_total']]);
                } else {
                    DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->update(['precio_ud' => $productos['precio_ud']]);
                }
            }
        }
        foreach ($this->productos_pedido_borrar as $productos) {
            if (isset($productos['id'])) {
                DB::table('productos_pedido')->where('id', $productos['id'])->limit(1)->delete();
            }
        }
        event(new \App\Events\LogEvent(Auth::user(), 4, $pedido->id));

        // Alertas de guardado exitoso
        if ($pedidosSave) {

            //Update factura relacionada si existe
            $factura = Facturas::where('pedido_id', $this->identificador)->first();
            if($factura){
                $factura->update(
                    ['precio' => $this->precio,
                    'cliente_id' => $this->cliente_id,
                    'gastos_envio' => $this->gastos_envio,
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
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
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
        event(new \App\Events\LogEvent(Auth::user(), 4, $pedido->id));

        // Alertas de guardado exitoso
        if ($pedidosSave) {
            $this->alert('success', '¡Pedido enviado a Almacén!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
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
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el Pedido? No hay vuelta atrás', [
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
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
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
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
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
                'showConfirmButton' => true,
                'onConfirmed' => 'aceptarPedido',
                'confirmButtonText' => 'Sí',
                'showDenyButton' => true,
                'denyButtonText' => 'No',
                'timerProgressBar' => true,
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
            'showConfirmButton' => true,
            'onConfirmed' => 'rechazarPedido',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
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
            // Muestra una alerta al usuario indicando que el producto no se encontró
            $this->alert('error', 'Producto no encontrado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
            return;
        }

        $precioUnitario = $this->obtenerPrecioPorTipo($producto);
        $precioTotal = $precioUnitario * $this->unidades_producto;

        $producto_existe = false;
        $producto_existe_sincargo =false;
        foreach ($this->productos_pedido as $productoPedido) {
            if ($productoPedido['producto_pedido_id'] == $id) {
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
                    if ($productoPedido['producto_pedido_id'] == $id) {
                        if ($productoPedido['precio_ud'] == 0) {
                        $key=$index;
                        }
                    }
                }
				$this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
			} else {
			$this->productos_pedido[] = [
                'producto_pedido_id' => $id,
                'unidades' => $this->unidades_producto,
                'precio_ud' => 0,
                'precio_total' => 0
            ];}

		} else{


			if ($producto_existe) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $id) {
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
					'producto_pedido_id' => $id,
					'unidades' => $this->unidades_producto,
					'precio_ud' => $precioUnitario,
					'precio_total' => $precioTotal
				];
			}

		}

        $this->sinCargo = false;

        $this->producto_seleccionado = 0;
        $this->unidades_producto = 0;
        $this->unidades_caja_producto = 0;
        $this->unidades_pallet_producto = 0;
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
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
            $this->precioEstimado = $this->gastos_envio;
            $this->gastos_envio_iva = $this->gastos_envio * 0.21;
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

        if($this->gastos_envio != 0 && $this->gastos_envio != null && is_numeric($this->gastos_envio)){
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
            event(new \App\Events\LogEvent(Auth::user(), 10, $pedido->id));
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

    $datos = [
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
    ];

    $pdf = PDF::loadView('livewire.pedidos.pdf-component', $datos)->setPaper('a4', 'vertical')->output();

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

            Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new PedidoMail($pdf, $cliente,$pedido,$productos));

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
                Mail::to($this->emailNuevo)->cc($this->emailNuevo)->bcc($emailsDireccion)->send(new PedidoMail($pdf, $cliente,$pedido,$productos));
            }else{
                Mail::to($cliente->email)->bcc($emailsDireccion)->send(new PedidoMail($pdf, $cliente,$pedido,$productos));
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
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'ok',
            'timerProgressBar' => true,
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

}
