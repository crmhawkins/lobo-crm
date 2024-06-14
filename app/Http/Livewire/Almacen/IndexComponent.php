<?php

namespace App\Http\Livewire\Almacen;

use Illuminate\Support\Facades\Auth;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\StockEntrante;
use App\Models\Facturas;
use App\Models\Productos;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Albaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Alertas;
use App\Models\Almacen;
use App\Models\StockRegistro;
use App\Models\User;
use App\Models\RegistroEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransporteMail;



class IndexComponent extends Component
{
    // public $search;
    use LivewireAlert;
    public $pedidos_pendientes = [];
    public $pedidos_preparacion = [];
    public $pedidos_enviados = [];
    public $fecha_salida;
    public $empresa_transporte;
    public $pedidoEnRutaId;
    public $email_transporte;
    public $observaciones_transporte;

    

    public function enviarEmailTransporte(){

        if($this->email_transporte == null){
            $this->alert('error', '¡Introduzca un email!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        

        

        $identificador = $this->pedidoEnRutaId;
        $pedido = Pedido::find($identificador);
        if (!$pedido) {
            $this->alert('error', 'Pedido no encontrado.');
            return;
        }
        $cliente = Clients::find($pedido->cliente_id);

        // Buscar el albarán asociado con el ID del pedido
        $albaran = Albaran::where('pedido_id', $pedido->id)->first();
        $Iva = true;
        if (!$albaran) {
            $this->alert('error', 'Albarán no encontrado para el pedido especificado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

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
                //dd($producto);
                $productos[] = [
                    'nombre' => $producto->nombre,
                    'cantidad' => $productoPedido->unidades,
                    'precio_ud' => $productoPedido->precio_ud,
                    'precio_total' => $productoPedido->precio_total,
                    'iva' => $producto->iva,
                    'productos_caja' => isset($producto->unidades_por_caja) ? $producto->unidades_por_caja : null,
                    'productos_pallet' => isset($producto->cajas_por_pallet) ? $producto->cajas_por_pallet : null,
                    'num_cajas' => isset($producto->unidades_por_caja) ? floor($productoPedido->unidades / $producto->unidades_por_caja) : null,
                    'num_pallet' => isset($producto->cajas_por_pallet) ? floor(($productoPedido->unidades / $producto->unidades_por_caja) / $producto->cajas_por_pallet) : null,
                    'lote_id' => isset($stockEntrante->orden_numero) ? $stockEntrante->orden_numero : '-----------' ,
                    'peso_kg' => ($producto->peso_neto_unidad * $productoPedido->unidades) / 1000,
                ];
            }
        }

        $datos = [
        'conIva' => $Iva,
        'pedido' => $pedido ,
        'cliente' => $cliente,
        'productos' => $productos,
        'num_albaran' => $num_albaran = $albaran->num_albaran,
        'fecha_albaran' => $fecha_albaran = $albaran->fecha,
        'almacen' => $this->getAlmacenObject($pedido->almacen_id),
        ];

        // Generar y mostrar el PDF
        $pdf = PDF::loadView('livewire.almacen.pdf-component', $datos)->setPaper('a4', 'vertical')->output();

        try{
            Mail::to($this->email_transporte)->send(new TransporteMail($pdf, $datos, $this->observaciones_transporte));

            $registroEmail = new RegistroEmail();
            $registroEmail->factura_id = null;
            $registroEmail->pedido_id = $pedido->id;
            $registroEmail->cliente_id = $pedido->cliente_id;
            $registroEmail->email = $this->email_transporte;
            $registroEmail->user_id = Auth::user()->id;
            $registroEmail->save();

            $this->alert('success', '¡Email enviado!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'timerProgressBar' => true,
            ]);

        }catch(\Exception $e){
                dd($e);
            $this->alert('error', '¡No se ha podido enviar el email!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
        
    }

    public function mount()
    {
        $userAlmacenId = Auth::user()->almacen_id; // Obtiene el almacen_id del usuario autenticado
        // Filtrar pedidos basados en almacen_id
        if ($userAlmacenId == 0) {
            // El usuario puede ver todos los pedidos
            $this->pedidos_pendientes = Pedido::where('estado', 2)->get();
            $this->pedidos_preparacion = Pedido::where('estado', 3)->get();
            $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->get();
            // $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->where('tipo_pedido_id', 0)->get();
        } else {
            // El usuario solo puede ver los pedidos de su almacén
            $this->pedidos_pendientes = Pedido::where('estado', 2)->where('almacen_id', $userAlmacenId)->get();
            $this->pedidos_preparacion = Pedido::where('estado', 3)->where('almacen_id', $userAlmacenId)->get();
            $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])
            ->where('almacen_id', $userAlmacenId)
            ->get();

            if(Auth::user()->user_department_id == 2){
                $this->pedidos_pendientes = Pedido::where('estado', 2)->where('almacen_id', 6)->get();
                $this->pedidos_preparacion = Pedido::where('estado', 3)->where('almacen_id', 6)->get();
                $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->where('almacen_id', 6)->get();
            }
                

            // $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])
            // ->where('almacen_id', $userAlmacenId)
            // ->where('tipo_pedido_id', 0)
            // ->get();
        }
        
    }

    public function render()
    {

        return view('livewire.almacen.index-component');
    }

    public function getNombreCliente($id){
        return Clients::where('id', $id)->first()->nombre;
    }

    public function getAlmacenObject($id){
        return Almacen::find($id);
    }

    public function getAlmacen($id){
        $almacen = Almacen::find($id);
        if (!$almacen){
            return 'Almacen no asignado';
        }

        return  $almacen->almacen;
    }
    
    public function getListeners()
    {
        return [
            'prepararPedido',
            'enRuta',
            'mostrarAlbaran',
            'comprobarStockPedido',
            'recarga',
            'enviarEmailTransporte'
        ];
    }

    public function completarPedido($id){
        $this->alert('success', '¡Pedido completado!', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'timerProgressBar' => true,
        ]);

        $pedido = Pedido::find($id);
        $pedido->update(['estado' => 5]);
        $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->get();
    }

    public function prepararPedido($identificador)
    {

        $pedido = Pedido::find($identificador);
        $pedidosSave = $pedido->update(['estado' => 3]);
        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Preparación',
                'descripcion' => 'El pedido nº ' . $pedido->id.' esta en preparación',
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
                enviarMensajeWhatsApp('pedido_preparacion', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_preparacion', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_preparacion', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_preparacion', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_preparacion', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_preparacion', $data, $buttondata, $phone);
            }

            

            $this->alert('success', '¡Pedido en preparación!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
            $userAlmacenId = Auth::user()->almacen_id; // Obtiene el almacen_id del usuario autenticado

            if ($userAlmacenId == 0) {
                // El usuario puede ver todos los pedidos
                $this->pedidos_pendientes = Pedido::where('estado', 2)->get();
                $this->pedidos_preparacion = Pedido::where('estado', 3)->get();
            } else {
                // El usuario solo puede ver los pedidos de su almacén
                $this->pedidos_pendientes = Pedido::where('estado', 2)->where('almacen_id', $userAlmacenId)->get();
                $this->pedidos_preparacion = Pedido::where('estado', 3)->where('almacen_id', $userAlmacenId)->get();
            }

        } else {
            $this->alert('error', '¡No se ha podido poner en preparación el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function asignarPedidoEnRutaId($pedidoId)
    {
        $this->pedidoEnRutaId = $pedidoId;
    }

 

    public function enRuta()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [   
                'pedidoEnRutaId' => 'required',
                'fecha_salida' => 'required',
                'empresa_transporte' => 'required',
            ],
            // Mensajes de error
            [
                'pedidoEnRutaId.required' => 'No se ha podido identificar el pedido.',
                'fecha_salida.required' => 'Indique fecha de salida.',
                'empresa_transporte.required' => 'Ingrese empresa de transporte',
            ]
        );

        $identificador = $this->pedidoEnRutaId;
        $pedido = Pedido::find($identificador);
        $pedidosSave = $pedido->update(['estado' => 8,
                                        'fecha_salida' => $this->fecha_salida,
                                        'empresa_transporte' => $this->empresa_transporte,
                                        
                                    ]);

        if ($pedidosSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: En Ruta ',
                'descripcion' => 'El pedido nº ' . $pedido->id . ' esta en ruta',
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
                
                enviarMensajeWhatsApp('pedido_ruta', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_ruta', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_ruta', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_ruta', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $pedido->almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_ruta', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $pedido->almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_ruta', $data, $buttondata, $phone);
            }


            $this->alert('success', '¡Pedido en Ruta!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);

            $userAlmacenId = Auth::user()->almacen_id; // Obtiene el almacen_id del usuario autenticado
            if ($userAlmacenId == 0) {
                // El usuario puede ver todos los pedidos
                $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])->get();
            } else {
                // El usuario solo puede ver los pedidos de su almacén
                $this->pedidos_enviados = Pedido::whereIn('estado', [4, 8])
                ->where('almacen_id', $userAlmacenId)
                ->get();
            }

        } else {
            $this->alert('error', '¡No se ha podido poner en preparación el pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function mostrarAlbaran($pedidoId,$Iva)
    {
        // Buscar el albarán asociado con el ID del pedido
        $albaran = Albaran::where('pedido_id', $pedidoId)->first();

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
        'conIva' => $Iva,
        'pedido' => $pedido ,
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
    public function comprobarStockPedido($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) {
            $this->alert('error', 'Pedido no encontrado.');
            return;
        }

        $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
        $almacenId = $pedido->almacen_id;
        $mensaje = "Comprobación de stock para el pedido: {$pedido->id}\n";

        foreach ($productosPedido as $productoPedido) {
            $producto = Productos::find($productoPedido->producto_pedido_id);
            $stockTotal = StockEntrante::whereHas('stock', function ($query) use ($almacenId) {
                                $query->where('almacen_id', $almacenId);
                            })
                            ->where('producto_id', $productoPedido->producto_pedido_id)
                            ->where('cantidad', '>', 0)
                            ->get();

            //dd($stockTotal);
            $stockRegistroTotal = 0;
            //sumar el stock registro
            $total = 0;
            // $arr = [];
            // $arr2 = [];
            foreach ($stockTotal as $stock) {
                
                $stockRegistro = StockRegistro::where('stock_entrante_id', $stock->id)->get();
                if($stockRegistro->count() > 0  ){
                    foreach ($stockRegistro as $stockReg) {
                        $stockRegistroTotal += $stockReg->cantidad;
                        
                    }
                    if($stock->cantidad - $stockRegistroTotal > 0){
                        $total += $stock->cantidad - $stockRegistroTotal;
                       // $arr[] = $stock;
                    }else{
                        //dd($stock);
                    }
                }else{
                    //$arr2[] = $stock;
                    $total += $stock->cantidad;
                }

            }
            
            $stockTotal = $stockTotal->sum('cantidad') - $stockRegistroTotal;
           

            $mensaje .= "Producto: {$producto->nombre}, Requerido: {$productoPedido->unidades}, En Stock: {$total} - ";
            if ($total >= $productoPedido->unidades) {
                $mensaje .= "Stock suficiente.\n";
            } else {
                $mensaje .= "Stock insuficiente.\n";
            }
        }

        $this->alert('info', $mensaje, [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'allowOutsideClick'=> false,

            'onConfirmed' => 'recarga',
            'confirmButtonText' => 'Entendido',
        ]);

    }
    public function recarga()
    {
        return redirect()->route('almacen.index');
    }

}


