<?php

namespace App\Http\Livewire\Pedidos;

use App\Models\Productos;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\ProductoLote;
use App\Models\Alertas;
use App\Models\PedidosStatus;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AnotacionesClientePedido;
use App\Models\ProductoPrecioCliente;
use App\Models\Almacen;
use App\Models\Iva;
use App\Models\User;
use App\Mail\PedidoMail;
use App\Models\Stock;
use App\Models\StockEntrante;
use App\Models\StockRegistro;
use App\Models\ProductosMarketing;
use App\Models\ProductosPedidoPack;

use App\Models\ProductosMarketingPedido;
use App\Models\ProductosMarketingPedidoPack;
use App\Models\Direcciones;

use Illuminate\Support\Facades\Mail;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $almacen_id = 0;
    public $porcentaje_descuento = 3; // Nuevo campo para el descuento personalizado
    public $cliente_id;
    public $nombre;
    public $precio = 0;
    public $precioEstimado = 0;
    public $precioSinDescuento;
    public $estado = 1;
    public $direccion_entrega;
    public $provincia_entrega;
    public $localidad_entrega;
    public $cod_postal_entrega;
    public $orden_entrega;
    public $fecha;
    public $observaciones;
    public $tipo_pedido_id = 0;
    public $productos_pedido = [];
    public $productos;
    public $descuento = 0;
    public $clientes;
    public $unidades_producto = 0;
    public $addProducto = 0;
    public $producto_seleccionado;
    public $unidades_pallet_producto;
    public $unidades_caja_producto;
    public $precio_crema;
    public $precio_vodka07l;
    public $precio_vodka175l;
    public $precio_vodka3l;
    public $bloqueado;
    public $porcentaje_bloq;
    public $porcentaje_sincargo = 0;
	public $sinCargo = false;
    public $anotacionesProximoPedido = [];
    public $productosPecioCliente = [];
    public $almacenes = [];
    public $numero;

    public $subtotal = 0;
    public $iva_total = 0;
    public $descuento_total = 0;
    public $npedido_cliente;
    
    public $gastos_envio;
    public $transporte;
    public $gastos_envio_iva;
    public $isAlmacenOnline = false;
    public $alertaAdmin = false;
    public $isMarketing = false;
    public $cliente;
    public $datos_transporte;
    public $gastos_transporte;
    public $productosMarketing = []; // Nueva propiedad para los productos de marketing
    public $productos_marketing_pedido = [];
    public $producto_marketing_seleccionado;
    public $direcciones = [];
    public $direccion_seleccionada = 'default';

    public $precio_producto_marketing = 0.01;

    public $direccionPorDefecto;
    public $localidadPorDefecto;
    public $provinciaPorDefecto;
    public $codPostalPorDefecto;

    public function mount()
    {
        

       // Inicializar la colección de productos
        // Obtener todos los productos, ordenados por grupo y orden dentro del grupo
        $this->productos = Productos::orderByRaw("CASE WHEN orden IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'orden' al final
        ->orderBy('orden', 'asc')  // Ordenar primero por orden
        ->orderByRaw("CASE WHEN grupo IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'grupo' al final
        ->orderBy('grupo', 'asc')  // Luego ordenar por grupo
        ->orderBy('nombre', 'asc')  // Finalmente, ordenar alfabéticamente por nombre
        ->get();



        //dd($this->productos);


      


        //dd($this->productos);
        $this->productosMarketing = ProductosMarketing::all(); // Cargar productos de marketing

        $this->clientes = Clients::where('estado', 2)
            ->orderBy('nombre', 'asc') // Ordenar clientes por nombre
            ->get();

        // Si el usuario autenticado es comercial, solo ve sus clientes asociados.
        if (Auth::user()->role == 3) {
            $this->clientes = Clients::where('comercial_id', Auth::user()->id)
                ->where('estado', 2)
                ->orderBy('nombre', 'asc') // Ordenar clientes por nombre
                ->get();
        }

        if (Auth::user()->user_department_id == 2) {
            $this->clientes = Clients::where('comercial_id', Auth::user()->id)
                ->orWhere('delegacion_COD', 0)
                ->orWhere('delegacion_COD', 16)
                ->where('estado', 2)
                ->orderBy('nombre', 'asc') // Ordenar clientes por nombre
                ->get();

            $this->almacen_id = 6;
        }

        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->estado = 1;
        $this->cliente_id = null;
        $this->almacenes = Almacen::all(); 
        $this->numero = Pedido::whereYear('created_at', Carbon::now()->year)->max('numero') + 1;
        //dd(Pedido::whereYear('created_at', Carbon::now()->year)->max('numero'));
        $this->cargarDirecciones();
    }
    public function isOnline(){
        $this->almacen_id = 6;
        $this->isAlmacenOnline = true;
    }

    public function isNotOnline(){
        $this->isAlmacenOnline = false;

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

            $this->precio_crema = $cliente->precio_crema;
            $this->precio_vodka07l = $cliente->precio_vodka07l;
            $this->precio_vodka175l = $cliente->precio_vodka175l;
            $this->precio_vodka3l = $cliente->precio_vodka3l;
            $this->porcentaje_bloq = $cliente->porcentaje_bloq;
            $this->direcciones = $cliente->direcciones;

            // Establecer la dirección por defecto
            $this->direccion_seleccionada = 'default';

            $this->productosPecioCliente = ProductoPrecioCliente::where('cliente_id', $this->cliente_id)->get();

            $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->cliente_id)->where('estado', 'pendiente')->get();

            if (count($this->anotacionesProximoPedido) > 0) {
                $this->alert('info', '¡El cliente tiene anotaciones pendientes!', [
                    'position' => 'center',
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Cerrar',
                    'timerProgressBar' => true,
                ]);
            }
        }
    }


    public function ComprobarStockPedido(){
        $data = [];
        foreach ($this->productos_pedido as $productoPedido) {
            $producto = Productos::find($productoPedido['producto_pedido_id']);
            $stock = $this->comprobarStock($producto, $productoPedido['unidades']);
            if(!$stock){
                $data[] = $producto->nombre;
            }
        }
        return $data;
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
        }



        return $hasStock;
    }


    public function aceptarPedido($id)
    {
        //si el rol es 2 , directamente se acepta el pedido
        
        if($this->porcentaje_descuento > $this->porcentaje_bloq || (isset( $this->cliente) && ( $this->cliente->credito < $this->precio ))){
            $this->bloqueado=true;
      
        }else{
            $this->bloqueado=false;
        }
        // dd($this->direccion_entrega, $this->localidad_entrega, $this->provincia_entrega, $this->cod_postal_entrega);
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'nombre' => 'nullable',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'tipo_pedido_id' => 'required',
                'observaciones' => 'nullable',
                'almacen_id' => 'required',
                'direccion_entrega' => 'nullable',
                'provincia_entrega' => 'nullable',
                'localidad_entrega' => 'nullable',
                'cod_postal_entrega' => 'nullable',
                'orden_entrega' => 'nullable',
                'descuento' => 'nullable',
                'porcentaje_descuento'=> 'nullable',
                'bloqueado'=> 'nullable',
                'gastos_envio' => 'nullable',
                'transporte' => 'nullable',
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
        $pedido = Pedido::find($id);
        $pedido->update($validatedData);
        if($this->bloqueado){
            return;
        }
        $pedidosSave = $pedido->update(['estado' => 2]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Aceptado en Almacén',
                'descripcion' => 'El pedido nº ' . $pedido->numero.'  del cliente '.$pedido->nombre_cliente.' ha sido aceptado',
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

            if(isset($dComercial) &&  $dComercial->telefono != null){
                $phone = '+34'.$dComercial->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) &&  $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) &&  $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) &&  $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) &&  $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_almacen', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) &&  $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
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

    

    public function completarAnotacion($id){
        $anotacion = AnotacionesClientePedido::find($id);
        $anotacion->update([
            'estado' => 'completado'
        ]);
        $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->cliente_id)->where('estado', 'pendiente')->get();
    }
    protected $listeners = ['refreshComponent' => '$refresh', 'closeModal' => 'closeModal'];

    public function render()
    {
        

        return view('livewire.pedidos.create-component');
    }

    public function domLoaded()
    {
        if(Auth::user()->isAdmin() && $this->alertaAdmin == false){

            //alert va a ser un pedido online? si es si, lanzar isOnline = true, si es no, isOnline = false
            $this->alert('info', '¿El pedido es online?', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Sí',
                'showDenyButton' => true,
                'denyButtonText' => 'No',
                'onConfirmed' => 'isOnline',
                'onDenied' => 'isNotOnline',
                'timer' => null
            ]);
            $this->alertaAdmin = true;
        }
        $this->emit('refreshComponent');
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        if (Auth::user()->role == 2) {

            if($this->almacen_id == 0 || $this->almacen_id == null){
                $this->alert('error', '¡Debe seleccionar un almacén!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'timerProgressBar' => true,
                ]);
                return;
                
            }
        }

        
        

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

        $total_iva  = 0;
        foreach ($this->productos_pedido as $productoPedido) {
             $producto = Productos::find($productoPedido['producto_pedido_id']);
             $precioBaseProducto = $this->obtenerPrecioPorTipo($producto);
             //ver que iva tiene el producto
                $iva = Iva::find($producto->iva_id);
                //dd($iva);
                if($iva){
                    //dd($iva);
                    if($this->descuento == 1){
                        $total_iva += (($productoPedido['precio_ud'] * $productoPedido['unidades']) * (1 - ($this->porcentaje_descuento / 100))) * ($iva->iva / 100);
                       
                    }else{
                        $total_iva += (($productoPedido['precio_ud'] * $productoPedido['unidades'])) * ($iva->iva / 100);
                        
                    }
                }
             
             
            // Compara el precio unitario del producto en el pedido con el precio base del cliente
            if ($productoPedido['precio_ud'] != $precioBaseProducto && $productoPedido['precio_ud'] != 0) {
                $this->bloqueado = true;
                //dd('bloqueado');
                //break; // Si encuentra una modificación en los precios, no necesita seguir comprobando
            }
         }

         

        //  if($this->gastos_envio != 0 && $this->gastos_envio != null && is_numeric($this->gastos_envio)){
        //     $this->gastos_envio_iva = $this->gastos_envio * 0.21;
        //     $total_iva += $this->gastos_envio_iva;
        // }


         if($this->gastos_transporte !=0 && $this->gastos_transporte != null && is_numeric($this->gastos_transporte)){
            $this->gastos_envio_iva = $this->gastos_transporte * 0.21;
            $total_iva += $this->gastos_envio_iva;
         }


         //dd($total_iva);

         $this->iva_total = $total_iva;

        
        //  dd($this->direccion_entrega , $this->localidad_entrega , $this->provincia_entrega , $this->cod_postal_entrega);
        // Validación de datos
        //si el rol es 2
        if (Auth::user()->role == 2) {
            $validatedData = $this->validate(
                [
                    'cliente_id' => 'required',
                    'nombre' => 'nullable',
                    'precio' => 'required',
                    'estado' => 'required',
                    'numero' => 'nullable',
                    'fecha' => 'required',
                    'tipo_pedido_id' => 'required',
                    'almacen_id' => 'required',
                    'observaciones' => 'nullable',
                    'direccion_entrega' => 'nullable',
                    'provincia_entrega' => 'nullable',
                    'localidad_entrega' => 'nullable',
                    'cod_postal_entrega' => 'nullable',
                    'orden_entrega' => 'nullable',
                    'descuento'=> 'nullable',
                    'porcentaje_descuento'=> 'nullable',
                    'bloqueado'=> 'nullable',
                    'subtotal' => 'nullable',
                    'iva_total' => 'nullable',
                    'descuento_total' => 'nullable',
                    'npedido_cliente' => 'nullable',
                    'gastos_envio' => 'nullable',
                    'transporte' => 'nullable',
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
    
        }else{
            $validatedData = $this->validate(
                [
                    'cliente_id' => 'required',
                    'nombre' => 'nullable',
                    'precio' => 'required',
                    'estado' => 'required',
                    'fecha' => 'required',
                    'tipo_pedido_id' => 'required',
                    'almacen_id' => 'nullable',
                    'observaciones' => 'nullable',
                    'direccion_entrega' => 'nullable',
                    'provincia_entrega' => 'nullable',
                    'localidad_entrega' => 'nullable',
                    'cod_postal_entrega' => 'nullable',
                    'orden_entrega' => 'nullable',
                    'descuento'=> 'nullable',
                    'porcentaje_descuento'=> 'nullable',
                    'bloqueado'=> 'nullable',
                    'subtotal' => 'nullable',
                    'iva_total' => 'nullable',
                    'descuento_total' => 'nullable',
                    'npedido_cliente' => 'nullable',
                    'gastos_envio' => 'nullable',
                    'transporte' => 'nullable',
                    'gastos_transporte' => 'nullable',
                    'numero' => 'nullable',
                ],
                // Mensajes de error
                [
                    'precio.required' => 'El precio del pedido es obligatorio.',
                    'cliente_id.required' => 'El cliente es obligatorio.',
                    'estado.required' => 'El estado del pedido es obligatoria.',
                    'fecha.required' => 'La fecha es obligatoria.',
                ]
            );
        }
        
        if(Auth::user()->user_department_id == 2){
            //add to validateData the department_id
            $validatedData['departamento_id'] = config('app.departamentos_pedidos')['Marketing']['id'];
        }else{
            $validatedData['departamento_id'] = config('app.departamentos_pedidos')['General']['id'];
        }

        if($this->isMarketing  && Auth::user()->isAdmin()){
            $validatedData['departamento_id'] = config('app.departamentos_pedidos')['Marketing']['id'];
        }


        $validatedData['numero'] = Pedido::whereYear('created_at', Carbon::now()->year)->max('numero') + 1;


        // Guardar datos validados
        $pedidosSave = Pedido::create($validatedData);

        

        foreach ($this->productos_marketing_pedido as $productoMarketing) {
            ProductosMarketingPedido::create([
                'pedido_id' => $pedidosSave->id,
                'producto_marketing_id' => $productoMarketing['producto_marketing_id'],
                'unidades' => $productoMarketing['unidades'],
                'precio_ud' => $productoMarketing['precio_ud'],
                'precio_total' => $productoMarketing['precio_total']
            ]);
        }

        
        try{
            // dd($pedidosSave->cliente->nombre);
            Mail::send([], [], function ($message) use ($pedidosSave) {
                $message->to('Alejandro.martin@serlobo.com')
                        ->subject('Nuevo Pedido Creado Nº '.$pedidosSave->numero .' - '.$pedidosSave->cliente->nombre)
                        ->html('<h1>Nuevo Pedido Creado</h1><p>El pedido número ' . $pedidosSave->id . ' ha sido creado para el cliente ' . $pedidosSave->nombre_cliente . '</p><br><a href="https://crmyerp.serlobo.com/admin/pedidos-edit/'.$pedidosSave->id.'" >Ir al pedido</a>');
            });

            // dd($pedidosSave->cliente->nombre);

            // Mail::send([], [], function ($message) use ($pedidosSave) {
            //     $message->to('ivan.mayol@hawkins.es')
            //             ->subject('Nuevo Pedido Creado Nº '.$pedidosSave->numero .' - '.$pedidosSave->cliente->nombre)

            //             ->html('<h1>Nuevo Pedido Creado</h1><p>El pedido número ' . $pedidosSave->id . ' ha sido creado para el cliente ' . $pedidosSave->cliente->nombre . '</p><br><a href="https://crmyerp.serlobo.com/admin/pedidos-edit/'.$pedidosSave->id.'" >Ir al pedido</a>');
            // });
            

        }catch(\Exception $e){
            $this->alert('error', '¡No se ha podido enviar el correo! ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
        


        if (Auth::user()->role == 2) {
            $hasStock = $this->ComprobarStockPedido();

            if(count($hasStock) > 0){
                $almacen = Almacen::find($this->almacen_id);

                try{
                    Mail::send([], [], function ($message) use ($hasStock, $almacen, $producto, $pedidosSave) {
                        $htmlContent = '<h1>Alerta de Stock Insuficiente para el pedido nº '.$pedidosSave->numero.' - '.$pedidosSave->nombre_cliente.'</h1>';
                        
                        foreach ($hasStock as $producto) {
                            $htmlContent .= '<p>El stock de ' . $producto . ' es insuficiente en el almacén de ' . $almacen->almacen . '.</p>';
                        }
                    
                        $message->to('Alejandro.martin@serlobo.com')
                                ->subject('Alerta de Stock Insuficiente para el pedido nº '.$pedidosSave->numero.' - '.$pedidosSave->nombre_cliente)
                                ->html($htmlContent);
                        // $message->to('ivan.mayol@hawkins.es')
                        // ->subject($producto.' - Alerta de Stock Bajo')
                        // ->html($htmlContent);
                    });
                }catch(\Exception $e){
                    //dd($e);
                }
                

                Alertas::create([
                    'user_id' => 13,
                    'stage' => 2,
                    'titulo' => 'Stock Insuficiente, Pedido Pendiente',
                    'descripcion' => 'El pedido nº ' . $pedidosSave->numero.' - '.$pedidosSave->nombre_cliente.' esta a la espera de stock',
                    'referencia_id' => $pedidosSave->id,
                    'leida' => null,
                ]);
            }
        }

        if( $this->bloqueado){
            Alertas::create([
                'user_id' => 13,
                'stage' => 2,
                'titulo' => 'Pedido Bloqueado: Pendiente de Aprobación',
                'descripcion' => 'El pedido nº ' . $pedidosSave->numero.' - '.$pedidosSave->nombre_cliente.' esta a la espera de aprobación',
                'referencia_id' => $pedidosSave->id,
                'leida' => null,
            ]);}

            $dGeneral = User::where('id', 13)->first();
            $dComercial = User::where('id', 14)->first();
            $administrativo1 = User::where('id', 17)->first();
            $administrativo2 = User::where('id', 18)->first();
            $almacenAlgeciras = User::where('id', 16)->first();
            $almacenCordoba = User::where('id', 15)->first();
            $data = [['type' => 'text', 'text' => $pedidosSave->id]];
            $buttondata = [$pedidosSave->id];

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

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedidosSave->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedidosSave->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_bloqueado', $data, $buttondata, $phone);
            }


            Alertas::create([
                    'user_id' => 13,
                    'stage' => 3,
                    'titulo' => 'Estado del Pedido: Recibido',
                    'descripcion' => 'El pedido nº ' . $pedidosSave->numero.' - '.$pedidosSave->nombre_cliente.' ha sido recibido',
                    'referencia_id' => $pedidosSave->id,
                    'leida' => null,
                ]);



                $dGeneral = User::where('id', 13)->first();
                $dComercial = User::where('id', 14)->first();
                $administrativo1 = User::where('id', 17)->first();
                $administrativo2 = User::where('id', 18)->first();
                $almacenAlgeciras = User::where('id', 16)->first();
                $almacenCordoba = User::where('id', 15)->first();
                $data = [['type' => 'text', 'text' => $pedidosSave->id]];
                $buttondata = [$pedidosSave->id];

                if(isset($dComercial) && $dComercial->telefono != null){
                    $phone = '+34'.$dComercial->telefono;
                    enviarMensajeWhatsApp('pedido_recibido', $data, $buttondata, $phone);
                }

                if(isset($dGeneral) && $dGeneral->telefono != null){
                    $phone = '+34'.$dGeneral->telefono;
                    enviarMensajeWhatsApp('pedido_recibido', $data, $buttondata, $phone);
                }

                if(isset($administrativo1) && $administrativo1->telefono != null){
                    $phone = '+34'.$administrativo1->telefono;
                    enviarMensajeWhatsApp('pedido_recibido', $data, $buttondata, $phone);
                }

                if(isset($administrativo2) && $administrativo2->telefono != null){
                    $phone = '+34'.$administrativo2->telefono;
                    enviarMensajeWhatsApp('pedido_recibido', $data, $buttondata, $phone);
                }

                if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedidosSave->almacen_id == 1){
                    $phone = '+34'.$almacenAlgeciras->telefono;
                    enviarMensajeWhatsApp('pedido_recibido', $data, $buttondata, $phone);
                }

                if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedidosSave->almacen_id == 2){
                    $phone = '+34'.$almacenCordoba->telefono;
                    enviarMensajeWhatsApp('pedido_recibido', $data, $buttondata, $phone);
                }


            foreach ($this->productos_pedido as $productos) {
                DB::table('productos_pedido')->insert([
                    'producto_pedido_id' => $productos['producto_pedido_id'],
                    'pedido_id' => $pedidosSave->id,
                    'unidades' => $productos['unidades'],
                    'precio_ud' => $productos['precio_ud'],
                    'precio_total' => $productos['precio_total']
                ]);
               // dd($productos);
                // Crear productos del pack después de guardar el pedido
                $producto = Productos::find($productos['producto_pedido_id']);
                if ($producto->is_pack) {
                    $productosAsociados = $productos['productos_asociados'];
                    $productosAsociadosMarketing = $productos['productos_asociados_marketing'];
                    foreach ($productosAsociados as $productoAsociado) {
                       // dd($productoAsociado);
                        ProductosPedidoPack::create([
                            'pedido_id' => $pedidosSave->id,
                            'producto_id' => $productoAsociado['id'],
                            'pack_id' => $producto->id,
                            'unidades' => $productoAsociado['unidades'], // Inicialmente 0, el usuario puede ajustar después
                        ]);
                    }

                    foreach ($productosAsociadosMarketing as $productoAsociadoMarketing) {
                        ProductosMarketingPedidoPack::create([
                            'pedido_id' => $pedidosSave->id,
                            'producto_id' => $productoAsociadoMarketing['id'],
                            'pack_id' => $producto->id,
                            'unidades' => $productoAsociadoMarketing['unidades'], // Inicialmente 0, el usuario puede ajustar después
                        ]);
                    }
                }


            }


       


            event(new \App\Events\LogEvent(Auth::user(), 3, $pedidosSave->id));

            // Alertas de guardado exitoso
            if ($pedidosSave) {
                //si el rol es 2 , directamente se acepta el pedido
                if (Auth::user()->role == 2 || Auth::user()->role == 1) {
                    $this->aceptarPedido($pedidosSave->id);
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

    public function deleteArticuloMarketing($index)
{
    // Elimina el producto de marketing del array utilizando el índice proporcionado
    unset($this->productos_marketing_pedido[$index]);

    // Reindexa el array para evitar problemas con índices no consecutivos
    $this->productos_marketing_pedido = array_values($this->productos_marketing_pedido);

    // Actualiza el precio estimado
    $this->setPrecioEstimadoMarketing();

    // Refresca el componente para reflejar los cambios
    $this->emit('refreshComponent');
}

public function getNombreProductoMarketing($id){
    $producto = ProductosMarketing::find($id);
    return $producto->nombre ?? '';
}

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'alertaGuardar',
            'checkLote',
            'closeModal',
            'domLoaded',
            'isOnline',
            'isNotOnline'
        ];
    }

    public function alertaGuardar()
    {
        $this->alert('warning', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submit',
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
        /*$producto = Productos::find($this->producto_seleccionado);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
        $this->unidades_producto = $this->unidades_caja_producto * $producto->unidades_por_caja;*/
		 $producto = Productos::find($this->producto_seleccionado);

		// Ensure that $this->unidades_caja_producto and $producto->cajas_por_pallet are treated as integers.
		$cajasPorPallet = (int)$producto->cajas_por_pallet;
		$unidadesCajaProducto = (int)$this->unidades_caja_producto;

		// Calculate unidades_pallet_producto by dividing unidades_caja_producto by cajas_por_pallet.
		// Both operands are cast to integers to prevent type mismatch errors.
		$this->unidades_pallet_producto = floor($unidadesCajaProducto / $cajasPorPallet);

		// Ensure that $producto->unidades_por_caja is treated as an integer.
		$unidadesPorCaja = (int)$producto->unidades_por_caja;

		// Calculate unidades_producto by multiplying unidades_caja_producto by unidades_por_caja.
		// The operands are cast to integers to ensure proper arithmetic operation.
		$this->unidades_producto = $unidadesCajaProducto * $unidadesPorCaja;
    }
    public function updateUnidad()
    {
        $producto = Productos::find($this->producto_seleccionado);
        $this->unidades_caja_producto = floor($this->unidades_producto / $producto->unidades_por_caja);
        $this->unidades_pallet_producto = floor($this->unidades_caja_producto / $producto->cajas_por_pallet);
    }

    public function updated($property){
        if($property == 'precio' && $this->isAlmacenOnline){
            
            //controlar valores no numericos

            if(!is_numeric($this->precio)){
              //comprobar si ha metido un numero con coma y cambiarlo a punto
                if(strpos($this->precio, ',') !== false){
                    $this->precio = str_replace(',', '.', $this->precio);
                    //comprobar si ahora no es un numero y tirar alerta si no lo es
                    if(!is_numeric($this->precio)){
                        //alerta debe introducir un valor numerico
                        $this->alert('error', '¡Debe introducir un valor numérico!', [
                            'position' => 'center',
                            'timer' => 1500,
                            'toast' => false,
                            'showConfirmButton' => false,
                            'timerProgressBar' => true,
                            'onClose' => $this->emit('closeModal'),
                            'allowOutsideClick' => false,
                        ]);
                        $this->precio = 0;
                    }
                }else{
                    //alerta debe introducir un valor numerico
                    $this->alert('error', '¡Debe introducir un valor numérico!', [
                        'position' => 'center',
                        'timer' => 1500,
                        'toast' => false,
                        'showConfirmButton' => false,
                        'timerProgressBar' => true,
                        'onClose' => $this->emit('closeModal'),
                        'allowOutsideClick' => false,
                    ]);
                    $this->precio = 0;
                }   
            }

            $this->descuento_total = $this->subtotal - $this->precio;
            if($this->descuento_total > 0)
            {
                $this->descuento = 1;
                $this->porcentaje_descuento = ($this->descuento_total / $this->subtotal) * 100;
            }
        }
    }

    public function deleteArticulo($id)
    {
        unset($this->productos_pedido[$id]);
        $this->productos_pedido = array_values($this->productos_pedido);
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }

    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }

    public function getNombreTablaMarketing($id)
    {
        $nombre_producto = ProductosMarketing::find($id)->nombre;
        return $nombre_producto;
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


    public function isClienteSeleccionado(){
        if($this->cliente_id == null){
           
            //alert cuando finalize que ejecute closeModal
            $this->alert('error', '¡Debe seleccionar un cliente!', [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
                'onClose' => $this->emit('closeModal'),
                'allowOutsideClick' => false,
            ]);


            return false;
        }
        return true;
    }

    public function closeModal(){
        return '';
    }

    public function setPrecioEstimadoMarketing()
{
    $this->precioEstimadoMarketing = 0;

    foreach ($this->productos_marketing_pedido as $producto) {
        $this->precioEstimadoMarketing += $producto['precio_total'];
    }

    // Asignar el precio final para productos de marketing
    $this->precioMarketing = number_format($this->precioEstimadoMarketing, 2, '.', '');
}



    public function actualizarPrecioTotalMarketing($index)
{
    $producto = $this->productos_pedido[$index];
    if (isset($producto['precio_ud']) && isset($producto['unidades']) && $producto['is_marketing'] == true) {
        $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['unidades'];
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

    // Si el usuario no ha especificado un precio, el precio por defecto es 0.01
    $precioUnitario = $this->precio_producto_marketing ?? 0.01;
    $precioTotal = $precioUnitario * $this->unidades_producto;

    // Añadir el producto de marketing al array de productos del pedido
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

    // Resetear campos
    $this->producto_marketing_seleccionado = 0;
    $this->unidades_producto = 0;
    $this->precio_producto_marketing = 0.01; // Volver al valor por defecto
    $this->setPrecioEstimadoMarketing();
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

        $productosAsociados = [];
        $productosAsociadosMarketing = [];
        // Verificar si el producto es un pack
        if ($producto->is_pack) {
            $productosAsociadosIds = json_decode($producto->products_id, true); // Asegúrate de que products_id sea un JSON válido
            $productosAsociadosIdsMarketing = json_decode($producto->products_id_marketing, true); // Asegúrate de que products_id_marketing sea un JSON válido
            if (is_array($productosAsociadosIds)) {
                foreach ($productosAsociadosIds as $productoAsociadoId) {
                    $productosAsociados[] = [
                        'id' => $productoAsociadoId,
                        'unidades' => 1 // Puedes ajustar la cantidad inicial según sea necesario
                    ];
                }
            } else {
                $this->alert('error', 'Error al procesar los productos del pack.', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => false,
                    'timerProgressBar' => true,
                ]);
                return;
            }

            if (is_array($productosAsociadosIdsMarketing)) {
                foreach ($productosAsociadosIdsMarketing as $productoAsociadoIdMarketing) {
                    $productosAsociadosMarketing[] = [
                        'id' => $productoAsociadoIdMarketing,
                        'unidades' => 1 // Puedes ajustar la cantidad inicial según sea necesario
                    ];
                }
            }else{
                $this->alert('error', 'Error al procesar los productos del pack.', [
                    'position' => 'center',
                    'timer' => 3000,
                ]);
            }
        }

        $precioUnitario = $this->obtenerPrecioPorTipo($producto);
        $precioTotal = $precioUnitario * $this->unidades_producto;

        $producto_existe = false;
        $producto_existe_sincargo = false;
        foreach ($this->productos_pedido as $productoPedido) {
            if ($productoPedido['producto_pedido_id'] == $id) {
                if ($productoPedido['precio_ud'] !== 0) {
                    $producto_existe = true;
                    break;
                } else {
                    $producto_existe_sincargo = true;
                }
            }
        }
        if ($this->sinCargo == true) {
            if ($producto_existe_sincargo) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $id) {
                        if ($productoPedido['precio_ud'] == 0) {
                            $key = $index;  
                        }
                    }
                }
                $this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
            } else {
                $this->productos_pedido[] = [
                    'producto_pedido_id' => $id,
                    'unidades' => $this->unidades_producto,
                    'precio_ud' => 0,
                    'precio_total' => 0,
                    'productos_asociados' => $productosAsociados, // Añadir productos asociados
                    'productos_asociados_marketing' => $productosAsociadosMarketing // Añadir productos asociados marketing
                ];
            }
        } else {
            if ($producto_existe) {
                foreach ($this->productos_pedido as $index => $productoPedido) {
                    if ($productoPedido['producto_pedido_id'] == $id) {
                        if ($productoPedido['precio_ud'] !== 0) {
                            $key = $index;
                        }
                    }
                }
                $this->productos_pedido[$key]['unidades'] += $this->unidades_producto;
                $this->productos_pedido[$key]['precio_ud'] = $precioUnitario;
                $this->productos_pedido[$key]['precio_total'] += $precioTotal;
            } else {
                $this->productos_pedido[] = [
                    'producto_pedido_id' => $id,
                    'unidades' => $this->unidades_producto,
                    'precio_ud' => $precioUnitario,
                    'precio_total' => $precioTotal,
                    'productos_asociados' => $productosAsociados, // Añadir productos asociados
                    'productos_asociados_marketing' => $productosAsociadosMarketing // Añadir productos asociados marketing
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

    private function mostrarProductosAsociados($productosAsociados)
    {
        // Aquí puedes implementar la lógica para mostrar los productos asociados al usuario
        // Por ejemplo, podrías usar una alerta o actualizar una propiedad para mostrar en la vista
        $nombresProductos = Productos::whereIn('id', $productosAsociados)->pluck('nombre')->toArray();
        $mensaje = 'Este pack incluye los siguientes productos: ' . implode(', ', $nombresProductos);
        $this->alert('info', $mensaje, [
            'position' => 'center',
            'timer' => 5000,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'Cerrar',
        ]);
    }


    public function mostrarProductosAsociadosMarketing($productosAsociadosMarketing)
    {
        // Aquí puedes implementar la lógica para mostrar los productos asociados al usuario
        // Por ejemplo, podrías usar una alerta o actualizar una propiedad para mostrar en la vista
        $nombresProductos = ProductosMarketing::whereIn('id', $productosAsociadosMarketing)->pluck('nombre')->toArray();
        $mensaje = 'Este pack incluye los siguientes productos: ' . implode(', ', $nombresProductos);
        $this->alert('info', $mensaje, [
            'position' => 'center',
            'timer' => 5000,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'Cerrar',
        ]);
    }

    public function actualizarPrecioTotal($index)
    {
        $producto = $this->productos_pedido[$index];
        if(isset($producto['precio_ud']) && isset($producto['unidades'])) {
            $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['unidades'];
        }
        $this->setPrecioEstimado();
    }
    private function obtenerPrecioPorTipo($producto)
    {

        if($this->productosPecioCliente->where('producto_id', $producto->id)->first()){
            return $this->productosPecioCliente->where('producto_id', $producto->id)->first()->precio;
        }

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
    }

    public function setPrecioEstimado()
    {
        $this->precioEstimado = 0;
        if($this->gastos_envio != 0 && $this->gastos_envio != null && is_numeric($this->gastos_envio)){
            //dd($this->gastos_envio);
            // $this->precioEstimado = $this->gastos_envio;
            //$this->gastos_envio_iva = $this->gastos_envio * 0.21;
        }

        if($this->gastos_transporte !=0 && $this->gastos_transporte != null && is_numeric($this->gastos_transporte)){
        
            $this->precioEstimado += $this->gastos_transporte;
            $this->gastos_envio_iva = $this->gastos_transporte * 0.21;
        }

        foreach ($this->productos_pedido as $producto) {
            $this->precioEstimado += $producto['precio_total'];
        }
        
        $this->precioSinDescuento = $this->precioEstimado;
        // Verificar si el descuento está activado
        if ($this->descuento) {

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
        $this->subtotal = number_format($this->precioSinDescuento,2, '.', '');

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

    public function getProductoImg()
    {
        $producto = Productos::find($this->producto_seleccionado);
        if ($producto != null) {
            return asset('storage/photos/' . $producto->foto_ruta);
        }

        $this->emit('refreshComponent');
    }


    public function confirmed()
    {
        return redirect()->route('pedidos.index');
    }

    public function getEstadoNombre()
    {
        return PedidosStatus::firstWhere('id', $this->estado)->status;
    }

    public function cargarDirecciones()
    {
        if ($this->cliente_id) {
            $this->direcciones = Direcciones::where('cliente_id', $this->cliente_id)->get();
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

    public function updatedClienteId($clienteId)
    {
        $this->cargarDirecciones();
    }
}
