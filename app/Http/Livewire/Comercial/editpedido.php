<?php

namespace App\Http\Livewire\Comercial;

use App\Models\Productos;
use App\Models\ClientesComercial;
use App\Models\PedidosComercial;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Iva;
use App\Models\User;
use App\Models\ProductosPedidoComercial;



class editpedido extends Component
{

    use LivewireAlert;
    public $cliente_id;
    public $precio = 0;
    public $precioEstimado = 0;
    public $estado = 1;
    public $observaciones;
    public $productos_pedido = [];
    public $productos;
    public $clientes;
    public $unidades_producto = 0;
    public $addProducto = 0;
    public $producto_seleccionado;
    public $unidades_pallet_producto;
    public $unidades_caja_producto;

    public $subtotal = 0;
    public $iva_total = 0;
    public $npedido;

    public $fecha;

    public $direccion_entrega;
    public $localidad_entrega;
    public $provincia_entrega;
    public $cod_postal_entrega;
    public $sinCargo;
    public $iva;
    public $identificador;
    public $productos_pedido_borrar = [];


    public function mount()
    {

        $this->productos = Productos::orderByRaw("CASE WHEN orden IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'orden' al final
        ->orderBy('orden', 'asc')  // Ordenar primero por orden
        ->orderByRaw("CASE WHEN grupo IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'grupo' al final
        ->orderBy('grupo', 'asc')  // Luego ordenar por grupo
        ->orderBy('nombre', 'asc')  // Finalmente, ordenar alfabéticamente por nombre
        ->get();

        $this->clientes = ClientesComercial::all();
        //si el usuario autenticado es comercial, solo ve sus clientes asociados.
        if (Auth::user()->role == 3 ){
            $this->clientes = ClientesComercial::where('comercial_id', Auth::user()->id)->get();
        }


        $this->pedido = PedidosComercial::find($this->identificador);
        // dd($this->pedido);
        //dd($this->id);
        $this->cliente_id = $this->pedido->cliente_id;

        $this->productos_pedido = ProductosPedidoComercial::where('pedido_id', $this->pedido->id)->get();
        //      dd($this->productos_pedido);

        $this->fecha = $this->pedido->fecha;
        $this->observaciones = $this->pedido->observaciones;
        $this->direccion_entrega = $this->pedido->direccion_entrega;
        $this->localidad_entrega = $this->pedido->localidad_entrega;
        $this->provincia_entrega = $this->pedido->provincia_entrega;
        $this->cod_postal_entrega = $this->pedido->cod_postal_entrega;
        $this->iva = $this->pedido->iva;
        $this->subtotal = $this->pedido->subtotal;
        $this->total = $this->pedido->total;
        $this->npedido = $this->pedido->npedido;
        $this->setPrecioEstimado();

    }

    public function confirmDelete() {
        $this->alert('warning', '¿Estás seguro de querer eliminar este pedido?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'Eliminar',
            'cancelButtonText' => 'Cancelar',
            'showCancelButton' => true,
            'onConfirmed' => 'Eliminar',
        ]);
    }


    public function selectCliente()
    {
        $cliente = ClientesComercial::find($this->cliente_id);
        $this->localidad_entrega = $cliente->localidad;
        $this->provincia_entrega = $cliente->provincia;
        $this->direccion_entrega = $cliente->direccion;
        $this->cod_postal_entrega = $cliente->cod_postal;

    }

    protected $rules = [
        'cliente_id' => 'required',
        'productos_pedido.*.precio_ud' => 'required|numeric|min:0',
        'productos_pedido.*.cantidad' => 'required|integer|min:1',
        // Otras reglas para otros campos
    ];

    public function actualizarPrecioTotal($index)
    {
        $producto = $this->productos_pedido[$index];

        $this->productos_pedido[$index]['precio_total'] = $producto['precio_ud'] * $producto['cantidad']  ;
        $this->setPrecioEstimado();
    }
    protected $listeners = ['refreshComponent' => '$refresh', 'updateWithoutRestrictions' => 'updateWithoutRestrictions', 'fileUpload' => 'handleFileUpload',
        'addDocumentos'];


    public function render()
    {
        return view('livewire.comercial.editpedido');
    }


    public function update()
    {


        $total_iva = 0;
        foreach ($this->productos_pedido as $productoPedido) {
            $producto = Productos::find($productoPedido['producto_id']);
            $precioBaseProducto = $this->obtenerPrecioPorTipo($producto);

            $iva = Iva::find($producto->iva_id);
            if($iva){
                //dd($iva);

                    $total_iva += (($productoPedido['precio_ud'] * $productoPedido['cantidad'])) * ($iva->iva / 100);

            }else{
                $total_iva += (($productoPedido['precio_ud'] * $productoPedido['cantidad'])) * (21 / 100);
            }

        }

        $this->iva_total = $total_iva;

        $this->total = $this->subtotal + $this->iva_total;

        // Validación de datos
        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',

            ],
            // Mensajes de error
            [
                'cliente_id.required' => 'El cliente es obligatorio.',

            ]
        );

        $pedido = PedidosComercial::find($this->identificador);
        // Guardar datos validados
        $pedidosSave = $pedido->update([
            'cliente_id' => $this->cliente_id,
            'npedido' => $this->npedido,
            'comercial_id' => Auth::user()->id,
            'fecha' => $this->fecha,
            'precio' => $this->precio,
            'iva' => $this->iva_total,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'direccion_entrega' => $this->direccion_entrega,
            'localidad_entrega' => $this->localidad_entrega,
            'cod_postal_entrega' => $this->cod_postal_entrega,
            'provincia_entrega' => $this->provincia_entrega,
            'observaciones' => $this->observaciones,
        ]);

        // dd($this->productos_pedido);

        foreach ($this->productos_pedido as $productos) {

            if (!isset($productos['id'])) {
                ProductosPedidoComercial::create([
                    'producto_id' => $productos['producto_id'],
                    'pedido_id' => $this->identificador,
                    'cantidad' => $productos['cantidad'],
                    'precio_ud' => $productos['precio_ud'],
                    'precio_total' => $productos['precio_total']]);
            } else {
                if ($productos['cantidad'] > 0) {
                    $unidades_finales = $productos['cantidad'] ;
                    DB::table('productos_pedido_comercial')->where('id', $productos['id'])->limit(1)->update(['cantidad' => $unidades_finales, 'precio_ud' => $productos['precio_ud'], 'precio_total' => $productos['precio_ud'] * $productos['cantidad']   ]);
                } else {
                    DB::table('productos_pedido_comercial')->where('id', $productos['id'])->limit(1)->update(['precio_ud' => $productos['precio_ud'] , 'precio_total' => $productos['precio_ud'] * $productos['cantidad']]);
                }
            }
        }
        foreach ($this->productos_pedido_borrar as $productos) {
            if (isset($productos['id'])) {
                DB::table('productos_pedido_comercial')->where('id', $productos['id'])->limit(1)->delete();
            }
        }

        if($pedidosSave){
            $this->alert('success', '¡Pedido actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }else{
            $this->alert('error', '¡No se ha podido actualizar el pedido!', [
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
            'updateWithoutRestrictions',
            'Eliminar'
        ];
    }

    public function alertaGuardar(){

        //alerta Esta seguro de guardar pedido con boton que accione la funcion update
        $this->alert('warning', '¿Está seguro de guardar el pedido?', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
        ]);


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


    public function aceptarPedido()
    {

        $cliente = ClientesComercial::find($this->cliente_id);

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
        $producto = Productos::find($this->productos_pedido[$id]['producto_id']);
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
                $cajas = ($this->productos_pedido[$id]['cantidad'] / $producto->unidades_por_caja);
                $pallets = floor($cajas / $producto->cajas_por_pallet);
                $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
                $unidades = '';
                if ($cajas_sobrantes > 0) {
                    $unidades = $this->productos_pedido[$id]['cantidad'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
                } else {
                    $unidades = $this->productos_pedido[$id]['cantidad'] . ' unidades (' . $pallets . ' pallets)';
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
                $this->productos_pedido[$index]['cantidad'] = $this->unidades_producto;
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

    // Asegúrate de que todos los productos actuales son arrays, incluyendo los que vienen de la BD.
    $this->productos_pedido = collect($this->productos_pedido)->map(function ($item) {
        return is_array($item) ? $item : $item->toArray();
    })->toArray();

    $precioUnitario = $this->obtenerPrecioPorTipo($producto);
    $precioTotal = $precioUnitario * $this->unidades_producto;

    $producto_existe = false;
    $producto_existe_sincargo = false;
    $key = null;

    // Verificar si el producto ya existe en la lista de productos
    foreach ($this->productos_pedido as $index => $productoPedido) {
        if ($productoPedido['producto_id'] == $id) {
            if ($productoPedido['precio_ud'] !== 0) {
                $producto_existe = true;
                $key = $index;
                break;
            } else {
                $producto_existe_sincargo = true;
                $key = $index;
            }
        }
    }

    if ($this->sinCargo == true) {
        if ($producto_existe_sincargo) {
            $this->productos_pedido[$key]['cantidad'] += $this->unidades_producto;
        } else {
            $this->productos_pedido[] = [
                'producto_id' => $id,
                'cantidad' => $this->unidades_producto,
                'precio_ud' => 0,
                'precio_total' => 0,
            ];
        }
    } else {
        if ($producto_existe) {
            $this->productos_pedido[$key]['cantidad'] += $this->unidades_producto;
            $precioUnitario = $this->productos_pedido[$key]['precio_ud'];
            $precioTotal = $precioUnitario * $this->unidades_producto;
            $this->productos_pedido[$key]['precio_total'] += $precioTotal;
        } else {
            $this->productos_pedido[] = [
                'producto_id' => $id,
                'cantidad' => $this->unidades_producto,
                'precio_ud' => $precioUnitario,
                'precio_total' => $precioTotal,
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
    // dd($this->productos_pedido);
}

    private function obtenerPrecioPorTipo($producto)
    {

        return $producto->precio ?? 0;
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


        foreach ($this->productos_pedido as $producto) {
            $this->precioEstimado += $producto['precio_total'];
        }



        // Asignar el precio final
        $this->precio = number_format($this->precioEstimado, 2, '.', '');
        $this->subtotal = number_format($this->precioEstimado,2, '.', '');
        $this->total = $this->precioEstimado * 0.21;
        $this->iva = $this->total;
        $this->total = $this->precioEstimado + $this->iva;
        $this->total = number_format($this->total, 2, '.', '');
        $this->iva = number_format($this->iva, 2, '.', '');

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
                    if ($productos['producto_id'] == $this->lote_seleccionado) {
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
        $cliente = ClientesComercial::find($id);
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


    public function Eliminar()
    {
        $pedidoId = $this->identificador;

        // Primero, elimina todos los productos_pedido asociados con este pedido
        $productosPedido = ProductosPedidoComercial::where('pedido_id', $pedidoId)->delete();

        // $productosPedido = ProductosPedido::where('pedido_id', $pedidoId)->delete();

        // Luego, elimina el pedido
        $pedido = PedidosComercial::find($pedidoId);
        if ($pedido) {
           // event(new \App\Events\LogEvent(Auth::user(), 10, $pedido->id));
            $pedido->delete();
        }

        return redirect()->route('comercial.pedidos');

    }
//     public function imprimirPedido()
//     {
//     $pedido = Pedido::find($this->identificador);
//     if (!$pedido) {
//         abort(404, 'Pedido no encontrado');
//     }

//     $cliente = Clients::find($pedido->cliente_id);
//     $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

//     // Preparar los datos de los productos del pedido
//     $productos = [];
//     foreach ($productosPedido as $productoPedido) {
//         $producto = Productos::find($productoPedido->producto_pedido_id);
//         if ($producto) {
//             $productos[] = [
//                 'nombre' => $producto->nombre,
//                 'cantidad' => $productoPedido->unidades,
//                 'precio_ud' => $productoPedido->precio_ud,
//                 'precio_total' => $productoPedido->precio_total,
//             ];
//         }
//     }
//     $iva= true;
//     // if($cliente->delegacion){

//     //     if ($cliente->delegacion && in_array($cliente->delegacion['id'], [15, 14, 13, 7])) {
//     //         $iva = false;
//     //     }
//     // }

//     $delegacionNombre = $cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene
//     if($delegacionNombre == '07 CANARIAS' || $delegacionNombre == '13 GIBRALTAR' || $delegacionNombre == '14 CEUTA' || $delegacionNombre == '15 MELILLA'){
//         $iva = false;
//     }

//     $configuracion = Configuracion::first();
//     $datos = [
//         'conIva' => $iva,
//         'pedido' => $pedido,
//         'cliente' => $cliente,
//         'localidad_entrega' => $pedido->localidad_entrega,
//         'direccion_entrega' => $pedido->direccion_entrega,
//         'cod_postal_entrega' => $pedido->cod_postal_entrega,
//         'provincia_entrega' => $pedido->provincia_entrega,
//         'fecha' => $pedido->fecha,
//         'observaciones' => $pedido->observaciones,
//         'precio' => $pedido->precio,
//         'descuento' => $pedido->descuento,
//         'productos' => $productos,
//         'configuracion' => $configuracion,
//     ];

//     $pdf = PDF::loadView('livewire.pedidos.pdf-component', $datos)->setPaper('a4', 'vertical');
//     $pdf->render();

//             $totalPages = $pdf->getCanvas()->get_page_count();

//             $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
//                 $text = "Página $pageNumber de $totalPages";
//                 $font = $fontMetrics->getFont('Helvetica', 'normal');
//                 $size = 10;
//                 $width = $canvas->get_width();
//                 $canvas->text($width - 100, 15, $text, $font, $size);
//             });

//             //$pdf->output();

//     try{

//         $emailsDireccion = [
//             'Alejandro.martin@serlobo.com',
//             'Ivan.ruiz@serlobo.com',
//             'Pedidos@serlobo.com'
//         ];
//         if($this->almacen_id == 2){
//             //push emailsDireccion
//             $emailsDireccion[] = 'Almacen.cordoba@serlobo.com';

//         }



//         if(count($this->emailsSeleccionados) > 0){
//             if($this->emailNuevo != null){
//                 array_push($this->emailsSeleccionados, $this->emailNuevo);
//             }

//             Mail::to($this->emailsSeleccionados[0])->cc($this->emailsSeleccionados)->bcc( $emailsDireccion)->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));
//             // Mail::to('ivan.mayol@hawkins.es')->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));

//             foreach($this->emailsSeleccionados as $email){
//                 $registroEmail = new RegistroEmail();
//                 $registroEmail->factura_id =null;
//                 $registroEmail->pedido_id = $pedido->id;
//                 $registroEmail->cliente_id = $pedido->cliente_id;
//                 $registroEmail->email = $email;
//                 $registroEmail->user_id = Auth::user()->id;
//                 $registroEmail->tipo_id = 2;
//                 $registroEmail->save();
//             }

//             if($this->emailNuevo != null){

//                 $registroEmail = new RegistroEmail();
//                 $registroEmail->factura_id =null;
//                 $registroEmail->pedido_id = $pedido->id;
//                 $registroEmail->cliente_id = $pedido->cliente_id;
//                 $registroEmail->email = $this->emailNuevo;
//                 $registroEmail->user_id = Auth::user()->id;
//                 $registroEmail->tipo_id = 2;
//                 $registroEmail->save();
//             }

//         }else{
//             if($this->emailNuevo != null){
//                 Mail::to($this->emailNuevo)->cc($this->emailNuevo)->bcc($emailsDireccion)->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos,  $iva));
//                 // Mail::to('ivan.mayol@hawkins.es')->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));

//             }else{
//                 Mail::to($cliente->email)->bcc($emailsDireccion)->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos,  $iva));
//                 // Mail::to('ivan.mayol@hawkins.es')->send(new PedidoMail($pdf->output(), $cliente,$pedido,$productos, $iva));

//             }

//             $registroEmail = new RegistroEmail();
//             $registroEmail->factura_id = null;
//             $registroEmail->pedido_id = $pedido->id;
//             $registroEmail->cliente_id = $pedido->cliente_id;
//             $registroEmail->email = $cliente->email;
//             $registroEmail->user_id = Auth::user()->id;
//             $registroEmail->tipo_id = 2;
//             $registroEmail->save();

//             if($this->emailNuevo != null){
//                 $registroEmail = new RegistroEmail();
//                 $registroEmail->factura_id = null;
//                 $registroEmail->pedido_id = $pedido->id;
//                 $registroEmail->cliente_id = $pedido->cliente_id;
//                 $registroEmail->email = $this->emailNuevo;
//                 $registroEmail->user_id = Auth::user()->id;
//                 $registroEmail->tipo_id = 2;
//                 $registroEmail->save();
//             }

//         }
//         $this->alert('success', '¡Pedido enviado correctamente!', [
//             'position' => 'center',
//             'timer' => 3000,
//             'toast' => false,
//             'showConfirmButton' => true,
//             'onConfirmed' => 'confirmed',
//             'confirmButtonText' => 'ok',
//             'timerProgressBar' => true,
//         ]);
//     }catch(\Exception $e){
//         //mostrarme el error
//         //dd($e);
//         $this->alert('error', '¡No se ha podido enviar el pedido!', [
//             'position' => 'center',
//             'timer' => 3000,
//             'toast' => false,
//         ]);
//     }

//     /*--return response()->streamDownload(
//         fn () => print($pdf),
//         "pedido_{$pedido->id}.pdf"
//     );*/
// }

}
