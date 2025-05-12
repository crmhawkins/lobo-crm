<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Alumno;
use App\Models\Cursos;
use App\Models\Productos;
use App\Models\Pedido;
use App\Models\Clients;
use App\Models\Facturas;
use App\Policies\ClientsEmailPolicy;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Alertas;
use Illuminate\Support\Facades\DB;
use App\Models\Iva;
use App\Models\ServiciosFacturas;
use App\Models\User;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $idpedido;
    public $numero_factura; // 0 por defecto por si no se selecciona ninguno
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descripcion;
    public $estado = "Pendiente";
    public $metodo_pago = "No Pagado";

    public $pedido;
    public $precio;
    public $pedido_id;
    public $cliente;
    public $clientes;
    public $cliente_id;
    public $producto_id;
    public $productos;
    public $cantidad;
    public $descuento;
    public $isFacturaRectificativa = false;
    public $tipo;
    public $observacionesDescarga;
    public $subtotal_pedido;
    public $iva_total_pedido;
    public $descuento_total_pedido;
    public $total;
    public $facturas;
    public $facturaSeleccionadaId;
    public $facturaSeleccionada;
    public $productos_pedido = [];
    public $descripcion_servicio;
    public $servicios = [];
    public $arrServicios = [];
    public $descripcionServicio;
    public $importeServicio;
    public $recargo = 0;
    public $total_recargo = 0;

    public function mount()
    {

        if (isset($this->idpedido)){
            $this->pedido_id = $this->idpedido;
            $this->pedido = Pedido::find($this->idpedido);
            $this->cliente_id = $this->pedido->cliente_id;
            $this->cliente = Clients::find($this->cliente_id);
            $this->observacionesDescarga = $this->cliente->observaciones;
            $diasVencimiento = $this->cliente->vencimiento_factura_pref;
            $this->fecha_vencimiento = Carbon::now()->addDays($diasVencimiento)->format('Y-m-d');
            $this->metodo_pago = $this->cliente->forma_pago_pref;
            $this->precio = $this->pedido->precio;
            if($this->pedido->descuento){
                $this->descuento = $this->pedido->porcentaje_descuento;
            }
            $this->subtotal_pedido = $this->pedido->subtotal;
            $this->iva_total_pedido = $this->pedido->iva_total;
            $this->descuento_total_pedido = $this->pedido->descuento_total;
            $this->total = $this->pedido->precio + $this->pedido->iva_total;
            $this->total = number_format($this->total, 2, '.', '');
        }

        $this->facturas = Facturas::all();
        $this->productos = Productos::all();
        $this->clientes = Clients::where('estado', 2)->get();
        $year = Carbon::now()->format('y'); // Esto obtiene el año en formato de dos dígitos, por ejemplo, "24" para 2024.
        $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)->max('numero_factura');

        if ($lastInvoice) {
            // Extrae el número secuencial de la última factura del año y lo incrementa
            $lastNumber = intval(substr($lastInvoice, 3)) + 1; // Asume que el formato es siempre "F24XXXX"
        } else {
            if($year == 24 ){
                $lastNumber = 150;
            }else{
                $lastNumber = 1;
            }

        }


        // si es el numero F240428, que lo excluya y coja el siguiente
        if($year == 24){
            if($lastNumber == 428){
                $lastNumber = 429; //FACTURA EN HOLDED
            }
        }

        //dd($lastNumber);
        // Genera el nuevo número de factura con relleno para asegurar 4 dígitos
        $this->numero_factura = 'F' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        //dd($this->numero_factura);
        $this->fecha_emision = Carbon::now()->format('Y-m-d');
    }


    public function onClienteChange()
    {
        $this->cliente = Clients::find($this->cliente_id);
        $this->observacionesDescarga = $this->cliente->observaciones;
        $diasVencimiento = $this->cliente->vencimiento_factura_pref;
        $this->fecha_vencimiento = Carbon::now()->addDays($diasVencimiento)->format('Y-m-d');
        $this->metodo_pago = $this->cliente->forma_pago_pref;
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
    public function updated($property){
        if($property == 'facturaSeleccionadaId'){
            $this->facturaSeleccionadaId = $this->facturaSeleccionadaId;
            $this->facturaSeleccionada = Facturas::find($this->facturaSeleccionadaId);
            $this->pedido_id = $this->facturaSeleccionada->pedido_id;
            $this->pedido = Pedido::find($this->pedido_id);
            $this->productos_pedido = DB::table('productos_pedido')->where('pedido_id', $this->pedido_id)->get();
            //productos_pedido to array
            $this->productos_pedido = json_decode(json_encode($this->productos_pedido), true);
            $this->subtotal_pedido = $this->pedido->subtotal;

            $this->iva_total_pedido = $this->pedido->iva_total;
            $this->descuento_total_pedido = $this->pedido->descuento_total;
            $this->total = $this->pedido->precio + $this->pedido->iva_total;

            $this->total = number_format($this->total, 2, '.', '');
        }
    }




    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }
    public function getNumeroFactura(){
        $year = Carbon::now()->format('y'); // Esto obtiene el año en formato de dos dígitos, por ejemplo, "24" para 2024.

        if($this->isFacturaRectificativa){
            $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)->where('tipo', 2)->max('numero_factura');
            if ($lastInvoice) {

                // Extrae el número secuencial de la última factura del año y lo incrementa
                $lastNumber = intval(substr($lastInvoice, 4)) + 1; // Asume que el formato es siempre "F24XXXX"
            } else {
                if($year ==  24 ){
                    $lastNumber = 20;
                }else{
                    $lastNumber = 1;
                }
            }

        }else{
            //donde tipo sea distinto de 2
            $lastInvoice = Facturas::whereYear('created_at', Carbon::now()->year)
            ->where(function($query) {
                $query->where('tipo', '!=', 2)
                      ->orWhereNull('tipo');
            })
            ->max('numero_factura');
                       // dd($lastInvoice);

            //numero de facturas perdidos
            // $facturas = Facturas::whereYear('created_at', Carbon::now()->year)->where('tipo', '!=', 2)->orWhere('tipo', null)->get();
            // //para coger el lastInvoice necesito que recorra todas las facturas no rectificativas, es decir de que no sean de tipo 2, y coja la que la ultima cuyo siguiente no sea consecutivo
            // //dd($facturas);
            // foreach($facturas as $index => $factura){

            //     $numero_factura = substr($factura->numero_factura, 1);
            //     $numero_factura = intval($numero_factura);
            //     //dd($numero_factura);

            //     if(!isset($facturas[$index + 1])){
            //         $lastInvoice = $factura->numero_factura;
            //         break;
            //     }
            //     $numero_factura_siguiente = substr($facturas[$index + 1]->numero_factura, 1);
            //     $numero_factura_siguiente = intval($numero_factura_siguiente);

            //     if($numero_factura + 1 != $numero_factura_siguiente){
            //         $lastInvoice = $factura->numero_factura;
            //         break;
            //     }

            //     //este numero_factura debe ser consecutivo por lo que la siguiente factura debe ser igual a la actual + 1
            // }

            //dd($lastInvoice);
            if ($lastInvoice) {
                // Extrae el número secuencial de la última factura del año y lo incrementa
                $lastNumber = intval(substr($lastInvoice, 3)) + 1; // Asume que el formato es siempre "F24XXXX"

            } else {
                if($year == 24 ){
                    $lastNumber = 150;
                }else{
                    $lastNumber = 1;
                }
            }

        }

        if($this->isFacturaRectificativa){

            $this->numero_factura = 'CN' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        }else{
            if($year == 24){
                if($lastNumber == 428){
                    $lastNumber = 429; //FACTURA EN HOLDED
                }
            }
            $this->numero_factura = 'F' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);

        }
        //dd( $lastInvoice);

    }

    public function render()
    {

        $this->getNumeroFactura();
        return view('livewire.facturas.create-component');
    }

    public function addArticulo()
    {

        $this->servicios = array_merge($this->servicios, [['descripcion' => $this->descripcionServicio, 'cantidad' => $this->cantidad, 'importe' => $this->importeServicio]]);
        $this->descripcionServicio = null;
        $this->cantidad = null;
        $this->importeServicio = null;
        //dd($this->servicios);
    }
    public function deleteServicio($index)
    {
        unset($this->servicios[$index]);
        $this->servicios = array_values($this->servicios);
    }

    public function selectCliente(){
        $this->cliente = Clients::find($this->cliente_id);
        $this->observacionesDescarga = $this->cliente->observaciones;
        $this->metodo_pago = $this->cliente->forma_pago_pref;
        //dd($this->cliente->forma_pago_pref);
    }

    public function calculoPrecio()
    {
        if(isset($this->cantidad) && isset($this->producto_id)){
           $producto = $this->productos->find($this->producto_id);
           $this->precio = $producto->precio * $this->cantidad;
        }
    }

    // Al hacer submit en el formulario
    public function submit()
    {

        if($this->isFacturaRectificativa){
            $this->tipo = 2;
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'numero_factura' => 'required',
                    'cliente_id' => 'required',
                    'pedido_id' => 'nullable',
                    'fecha_emision' => 'required',
                    'fecha_vencimiento' => '',
                    'descripcion' => '',
                    'estado' => 'nullable',
                    'precio' => 'nullable',
                    'metodo_pago' => 'nullable',
                    'producto_id' => 'nullable',
                    'cantidad' => 'nullable',
                    'descuento' => 'nullable',
                    'tipo' => 'required',

                ],
                // Mensajes de error
                [
                    'numero_factura.required' => 'Indique un nº de factura.',
                    'fecha_emision.required' => 'Ingrese una fecha de emisión',
                ]
            );
        }elseif (isset($this->idpedido)){
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'numero_factura' => 'required',
                    'cliente_id' => 'required',
                    'pedido_id' => 'nullable',
                    'fecha_emision' => 'required',
                    'fecha_vencimiento' => '',
                    'descripcion' => '',
                    'estado' => 'nullable',
                    'precio' => 'nullable',
                    'metodo_pago' => 'nullable',
                    'producto_id' => 'nullable',
                    'cantidad' => 'nullable',
                    'descuento' => 'nullable',
                    'subtotal_pedido' => 'nullable',
                    'iva_total_pedido' => 'nullable',
                    'descuento_total_pedido' => 'nullable',

                ],
                // Mensajes de error
                [
                    'numero_factura.required' => 'Indique un nº de factura.',
                    'fecha_emision.required' => 'Ingrese una fecha de emisión',
                    'id_pedido.min' => 'Seleccione un pedido',
                ]
            );
        }elseif($this->idpedido == null){


                $this->iva = 0;
                $this->iva_total_pedido = 0;
                $this->descuento_total_pedido = 0;
                $this->descuento = 0;
                $this->tipo = 3;

                //total y subtotal_pedido, hay que recorrer servicios si es mayor que 0 y sumar los totales, que son precio * cantidad
                $this->total = 0;
                $this->subtotal_pedido = 0;

                if(count($this->servicios) > 0){
                    foreach($this->servicios as $servicio){
                        $this->subtotal_pedido += $servicio['importe'] * $servicio['cantidad'];
                        $this->total += $servicio['importe'] * $servicio['cantidad'];
                    }
                }

                $this->precio = $this->total;
                $this->iva_total_pedido = ($this->subtotal_pedido * 21) / 100;
                $this->iva = $this->iva_total_pedido;
                $this->total = $this->total + $this->iva_total_pedido;



                //dd($this->numero_factura, $this->cliente_id, $this->pedido_id, $this->fecha_emision, $this->fecha_vencimiento, $this->descripcion, $this->estado, $this->precio, $this->metodo_pago, $this->producto_id, $this->cantidad, $this->descuento, $this->iva, $this->subtotal_pedido, $this->iva_total_pedido, $this->descuento_total_pedido, $this->descripcion_servicio, $this->tipo, $this->total);

            //validacion con servicios
            $validatedData = $this->validate(
                [
                    'numero_factura' => 'required',
                    'cliente_id' => 'required',
                    'pedido_id' => 'nullable',
                    'fecha_emision' => 'required',
                    'fecha_vencimiento' => '',
                    'descripcion' => 'nullable',
                    'estado' => 'nullable',
                    'precio' => 'nullable',
                    'metodo_pago' => 'nullable',
                    'producto_id' => 'nullable',
                    'cantidad' => 'nullable',
                    'descuento' => 'nullable',
                    'iva' => 'nullable',
                    'subtotal_pedido' => 'nullable',
                    'iva_total_pedido' => 'nullable',
                    'descuento_total_pedido' => 'nullable',
                    'descripcion_servicio' => 'nullable',
                    'tipo' => 'nullable',
                    'total' => 'nullable',
                ],
                // Mensajes de error
                [
                    'numero_factura.required' => 'Indique un nº de factura.',
                    'fecha_emision.required' => 'Ingrese una fecha de emisión',
                ]
            );
        }

        //si el pedido ya tiene factura, no se puede crear otra
        if($this->idpedido != null){
            $factura = Facturas::where('pedido_id', $this->idpedido)->first();
            if($factura){
                $this->alert('error', '¡El pedido ya tiene una factura asociada!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
                return;
            }
        }

        // Guardar datos validados
        $facturasSave = Facturas::create($validatedData);
        //event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));
        $pedidosSave =false;
        // Alertas de guardado exitoso
        if (isset($this->idpedido)){
            if($this->pedido->fecha_entrega != null || $this->pedido->fecha_entrega != ''){
                $pedidosSave = $this->pedido->update(['estado' => 5]);
            }
        }
        if ($facturasSave) {
            if($this->idpedido == null){
                if(count($this->servicios) > 0){
                    foreach($this->servicios as $servicio){
                        $servicios = new ServiciosFacturas();
                        $servicios->factura_id = $facturasSave->id;
                        $servicios->cantidad = $servicio['cantidad'];
                        $servicios->precio = $servicio['importe'];
                        $servicios->total = $servicio['importe'] * $servicio['cantidad'];
                        $servicios->descripcion = $servicio['descripcion'];
                        $servicios->save();
                    }
                }
            }else{
                $this->calcularTotales($facturasSave);
            }


            if($pedidosSave){
                Alertas::create([
                    'user_id' => 13,
                    'stage' => 3,
                    'titulo' => 'Estado del Pedido: Entregado ',
                    'descripcion' => 'El pedido nº ' . $this->pedido->id . ' ha sido entregado',
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
                    enviarMensajeWhatsApp('pedido_entregado', $data, $buttondata, $phone);
                }

                if(isset($dGeneral) && $dGeneral->telefono != null){
                    $phone = '+34'.$dGeneral->telefono;
                    enviarMensajeWhatsApp('pedido_entregado', $data, $buttondata, $phone);
                }

                if(isset($administrativo1) && $administrativo1->telefono != null){
                    $phone = '+34'.$administrativo1->telefono;
                    enviarMensajeWhatsApp('pedido_entregado', $data, $buttondata, $phone);
                }

                if(isset($administrativo2) && $administrativo2->telefono != null){
                    $phone = '+34'.$administrativo2->telefono;
                    enviarMensajeWhatsApp('pedido_entregado', $data, $buttondata, $phone);
                }

                if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null &&  $this->pedido->almacen_id == 1){
                    $phone = '+34'.$almacenAlgeciras->telefono;
                    enviarMensajeWhatsApp('pedido_entregado', $data, $buttondata, $phone);
                }

                if(isset($almacenCordoba) && $almacenCordoba->telefono != null &&  $this->pedido->almacen_id == 2){
                    $phone = '+34'.$almacenCordoba->telefono;
                    enviarMensajeWhatsApp('pedido_entregado', $data, $buttondata, $phone);
                }


            }


            $this->alert('success', 'Factura registrada correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la factura!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }


    public function calcularTotales($factura){
        $iva= 0;
        $total = 0;
        $recargo = $this->recargo;
        $recargo_total = 0;

        //si hay pedido id
        if(isset($factura) && isset($factura->pedido_id) && $factura->pedido_id != null){

            //calcular el recargo en base a $factura->precio, siendo recargo un porcentaje


            $recargo_total = (($factura->precio * $recargo) / 100);
            $total = $factura->precio + $recargo_total + $factura->iva_total_pedido;
            $iva = $factura->iva_total_pedido;

            $factura->iva = $iva;
            $factura->total = $total;
            $factura->recargo = $recargo;
            $factura->total_recargo = $recargo_total;
            $factura->save();

        }else{
            if(isset($factura) && isset($factura->precio) && $factura->precio != null){
                $recargo_total = (($factura->precio * $recargo) / 100);
                $total = $factura->precio ;
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

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'destroy',
            'listarPedido',
            'onClienteChange'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('facturas.index');
    }
}
