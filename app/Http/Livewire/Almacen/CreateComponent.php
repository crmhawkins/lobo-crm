<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Almacen;
use App\Models\Clients;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Albaran;
use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\Stock;
use App\Models\StockEntrante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Alertas;
use App\Models\StockSaliente;
use App\Models\StockRegistro;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\StockSubalmacen;
use App\Models\ProductosMarketing;
use App\Models\ProductosMarketingPedido;
use App\Models\Subalmacenes;
use App\Models\ProductosPedidoPack;


class CreateComponent extends Component
{

    use LivewireAlert;

    public $identificador;
    public $pedido;
    public $pedido_id;
    public $num_albaran;
    public $cliente;
    public $fecha;
    public $fecha_vencimiento;
    public $descripcion;
    public $productos;
    public $lotes;
    public $clientes;
    public $total_factura;
    public $productos_pedido = [];
    public $estado = "Pendiente";
    public $observaciones;
    public $descuento;
    public $almacen_id;
    public $pedido_almacen_id;
    public $observacionesDescarga;
    public $gastos_envio;
    public $gastos_envio_iva;
    public $transporte;

    public $productosSinStock = []; // Propiedad para almacenar los productos sin stock
    public $productosMarketingPedidos = [];

    public function mount()
    {
        
        $this->almacen_id = auth()->user()->almacen_id;
        $this->pedido = Pedido::find($this->identificador);
        $this->gastos_envio = $this->pedido->gastos_envio;
        $this->transporte = $this->pedido->transporte;
        $this->pedido_id = $this->pedido->id;
        $this->pedido_almacen_id = $this->pedido->almacen_id;
        $this->descuento = $this->pedido->descuento;
        $this->cliente = Clients::where('id', $this->pedido->cliente_id)->first();
        $this->observacionesDescarga = $this->cliente->observaciones;
        $this->productos = Productos::all();
        $this->lotes = StockEntrante::all();
        $this->clientes = Clients::all();
        $this->num_albaran = Albaran::count() + 1;
        $this->fecha = Carbon::now()->format('Y-m-d');
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->identificador)->get();
        $this->productosMarketingPedidos = ProductosMarketingPedido::where('pedido_id', $this->identificador)->get();

        foreach ($productos as $producto) {
            $productoModel = Productos::find($producto->producto_pedido_id);

            if($productoModel->is_pack){
                $productosAsociados = json_decode($productoModel->products_id);
                $productosAsociadosPedido = [];

                foreach($productosAsociados as $productoAsociado){
                    $productoAsociadoModel = ProductosPedidoPack::where('producto_id', $productoAsociado)->where('pedido_id', $this->identificador)->first();
                    if($productoAsociadoModel){
					
						$productosAsociadosPedido[] = [
							'id' => $productoAsociadoModel->producto_id,
							'nombre' => $productoAsociadoModel->producto->nombre,
							'unidades' => $productoAsociadoModel->unidades,
							'lote_id' => $productoAsociadoModel->lote_id,
						];
					}else{
						
						$productoAsociadoModel = Productos::find($productoAsociado);
								//dd($productoAsociadoModel->nombre);
								$productosAsociadosPedido[] = [
									'id' => $productoAsociado,
									'nombre' => $productoAsociadoModel->nombre,
									'unidades' => 0,
									'lote_id' => null
								];
					}
                }

            }


            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_pedido_id' => $producto->producto_pedido_id,
                'unidades_old' => $producto->unidades,
                'precio_ud' => $producto->precio_ud,
                'precio_total' => $producto->precio_total,
                'unidades' => 0,
                'borrar' => 0,
                'lote_id' => $producto->lote_id,
                'is_pack' => $productoModel->is_pack ?? false,


                'productos_asociados' => $productoModel->is_pack ? $productosAsociadosPedido : [],
            ];

            
        }
        
    }

    public function render()
    {
        return view('livewire.almacen.create-component');
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        $this->productosSinStock = []; // Reseteamos la lista de productos sin stock
        $productosMarketingPedidos = ProductosMarketingPedido::where('pedido_id', $this->pedido_id)->get();
        foreach ($productosMarketingPedidos as $productoMarketingPedido) {
            $hasStock = $this->HasStockMarketing($productoMarketingPedido);
    
            // Si no hay suficiente stock, detener el proceso
            if (!$hasStock) {
                //alerta stock insuficiente
                $this->alert('error', '¡No hay suficiente stock de Marketing en almacén para este pedido!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Aceptar',
                ]);
                return; // Detenemos la ejecución del submit si no hay stock suficiente
            }

            $this->registrarSalidaDeStockMarketing($subAlmacenMarketing, $productoMarketingPedido->unidades, $this->pedido, $productoMarketing);

        }
        $this->total_factura = $this->pedido->precio;
        // Validación de datos
        $validatedData = $this->validate(
            [
                'num_albaran' => 'required',
                'pedido_id' => 'required|numeric|min:1',
                'fecha' => 'required',
                'observaciones' => 'nullable',
                'total_factura' => '',
            ],
            // Mensajes de error
            [
                'num_albaran.required' => 'Indique un nº de factura.',
                'fecha.required' => 'Ingrese una fecha de emisión',
                'pedido_id.min' => 'Seleccione un pedido',
            ]
        );

        $pedido = Pedido::firstWhere('id', $this->pedido_id)->update(['estado' => 5]);

        // Guardar datos validados
        $facturasSave = Albaran::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));

        // Alertas de guardado exitoso
        if ($facturasSave) {
            $this->alert('success', 'Factura registrada correctamente!', [
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

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'destroy',
            'listarPedido',
            'GenerarAlbaran',
            'qrScanned' => 'handleQrScanned',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('almacen.index');
    }
    public function getNombreTabla($id)
    {
        $producto = $this->productos->where('id', $id)->first();
        $nombre_producto = isset($producto) ? $producto->nombre : '';
        return $nombre_producto;
    }


    public function getPesoTotal($id,$In)
    {
        $producto = $this->productos->where('id', $id)->first();
        $pesoUnidad = isset($producto) ? $producto->peso_neto_unidad : 0;
        $Cantidad = $this->productos_pedido[$In]['unidades_old'];
        $pesoTotal= ($pesoUnidad * $Cantidad)/1000;
        if(isset($this->productos_pedido[$In]['is_pack'])){
            if($this->productos_pedido[$In]['is_pack']){
                $pesoTotal = 0;
                foreach($this->productos_pedido[$In]['productos_asociados'] as $productoAsociado){
                    $productoAsociadoModel = Productos::find($productoAsociado['id']);
                    $pesoTotal += ($productoAsociado['unidades'] * $productoAsociadoModel->peso_neto_unidad) / 1000;

                }
            }
        }

        return $pesoTotal;

    }

    public function registrarSalidaDeStockMarketing($subAlmacenMarketing, $cantidad, $pedido, $productoMarketing)
    {
        // dd($subAlmacenMarketing, $cantidad, $pedido, $productoMarketing);
            // Registrar la salida de stock para productos de marketing
            // $stockRegistro = new StockSubalmacen();
            // $stockRegistro->subalmacen_id = $subAlmacenMarketing->id;
            // $stockRegistro->producto_id = $productoMarketing->id;
            // $stockRegistro->cantidad = $cantidad; // Reducimos el stock restando la cantidad
            // $stockRegistro->fecha = Carbon::now();
            // $stockRegistro->tipo_salida = "Venta";
            // $stockRegistro->save();
            // dd($subAlmacenMarketing->id);
            StockSubalmacen::create([
                'subalmacen_id' => $subAlmacenMarketing->id,
                'producto_id' => $productoMarketing->id,
                'cantidad' => $cantidad,
                'fecha' => Carbon::now(),
                'tipo_salida' => 'Venta',
                'observaciones' => 'Venta de producto de marketing'
            ]);



            return true; // Salida registrada exitosamente

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
    public function getUnidadesTabla($id)
    {
        $producto = Productos::find($this->productos_pedido[$id]['producto_pedido_id']);
        if($producto === null){
            return '';
           
        }
        if (isset($this->productos_pedido[$id]['unidades_old'])) {
            $uds_total = $this->productos_pedido[$id]['unidades_old'] + $this->productos_pedido[$id]['unidades'];
            $cajas = ($uds_total / $producto->unidades_por_caja);
            $pallets = floor($cajas / $producto->cajas_por_pallet);
            $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
            $unidades = '';
            if ($cajas_sobrantes > 0) {
                $unidades = $uds_total . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
            } else {
                $unidades = $uds_total . ' unidades (' . $pallets . ' pallets)';
            }
        } else {
            $cajas = ($this->productos_pedido[$id]['unidades'] / $producto->unidades_por_caja);
            $pallets = floor($cajas / $producto->cajas_por_pallet);
            $cajas_sobrantes = $cajas % $producto->cajas_por_pallet;
            $unidades = '';
            if ($cajas_sobrantes > 0) {
                $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets, y ' . $cajas_sobrantes . ' cajas)';
            } else {
                $unidades = $this->productos_pedido[$id]['unidades'] . ' unidades (' . $pallets . ' pallets)';
            }
        }

        return $unidades;
    }

    public function HasStockMarketing($productoMarketingPedido)
    {
        // Obtener el producto de marketing asociado al pedido
        $productoMarketing = ProductosMarketing::find($productoMarketingPedido->producto_marketing_id);
    
        if (!$productoMarketing) {
            $this->alert('error', 'Producto de marketing no encontrado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return false;
        }
    
        // Buscar el stock en subalmacenes de marketing
        $stockDisponible = $productoMarketing->stockEnAlmacen($this->pedido_almacen_id);
    
        // Verificar si hay suficiente stock
        if ($stockDisponible < $productoMarketingPedido->unidades) {
            // Añadir el producto a la lista de productos sin stock
            $this->productosSinStock[] = [
                'producto' => $productoMarketing->nombre,
                'stockDisponible' => $stockDisponible,
                'cantidadRequerida' => $productoMarketingPedido->unidades
            ];
    
            return false; // No hay stock suficiente, retorna false
        }
    
        return true; // Si hay suficiente stock, devuelve true
    }
    


    public function registrarStock(){
        $pedido = Pedido::find($this->identificador);

        $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
        foreach ($productosPedido as $productoPedido) {
            $producto = Productos::find($productoPedido->producto_pedido_id);
            $stockSeguridad =  $producto->stock_seguridad;
            $stockEntrante = StockEntrante::where('id',$productoPedido->lote_id)->first();
            if (!isset( $stockEntrante)){
                $stockEntrante = StockEntrante::where('lote_id',$productoPedido->lote_id)->first();
            }
            $almacen_id = Stock::find($stockEntrante->stock_id)->almacen_id;
            $almacen = Almacen::find($almacen_id);
            
                
                if ($stockEntrante) {

                    $stockRegistro = new StockRegistro();
                    $stockRegistro->stock_entrante_id = $stockEntrante->id;
                    $stockRegistro->cantidad = $productoPedido->unidades;
                    $stockRegistro->tipo = "Venta";
                    $stockRegistro->motivo = "Salida";
                    $stockRegistro->pedido_id = $pedido->id;
                    
                    $stockRegistro->save();
    
                    $stockSaliente = StockSaliente::create([
                        'stock_entrante_id' => $stockEntrante->id,
                        'producto_id' => $producto->id,
                        'cantidad_salida' => $productoPedido->unidades,
                        'fecha_salida' => Carbon::now(),
                        'pedido_id' => $pedido->id,
                        'tipo' => 'Pedido',
                        'motivo_salida' => 'Venta',
                        'almacen_origen_id' => $almacen->id,
                    ]);
                }


            
            
            $entradasAlmacen = Stock::where('almacen_id', $almacen->id)->get()->pluck('id');
            $productoLotes = StockEntrante::where('producto_id', $producto->id)->whereIn('stock_id', $entradasAlmacen)->get();
            foreach($productoLotes as $productoLote){
                //sumatorio de cantidad lotes segun el stockRegistro
                $stockRegistro = StockRegistro::where('stock_entrante_id', $productoLote->id)->sum('cantidad');
                $productoLote->cantidad = $productoLote->cantidad - $stockRegistro;
            }
            $sumatorioCantidad = $productoLotes->sum('cantidad');

            if ($sumatorioCantidad < $stockSeguridad) {
                $alertaExistente = Alertas::where('referencia_id', $producto->id . $almacen->id )->where('stage', 7)->first();
                if (!$alertaExistente) {
                    Alertas::create([
                        'user_id' => 13,
                        'stage' => 7,
                        'titulo' => $producto->nombre.' - Alerta de Stock Bajo',
                        'descripcion' =>'Stock de '.$producto->nombre. ' insuficiente en el almacen de ' . $almacen->almacen,
                        'referencia_id' =>$producto->id . $almacen->id ,
                        'leida' => null,
                    ]);

                    Mail::send([], [], function ($message) use ($producto, $almacen) {
                        $message->to('Alejandro.martin@serlobo.com')
                                ->subject($producto->nombre.' - Alerta de Stock Bajo')
                                ->html('<h1>Alerta de Stock Bajo</h1><p>El stock de '.$producto->nombre.' es insuficiente en el almacén de ' . $almacen->almacen . '.</p>');
                    });
                    
                    $dGeneral = User::where('id', 13)->first();
                    $administrativo1 = User::where('id', 17)->first();
                    $administrativo2 = User::where('id', 18)->first();

                    $data = [['type' => 'text', 'text' => $producto->nombre], ['type' => 'text', 'text' =>  $almacen->almacen]];
                    $buttondata = [];
                    

                    if(isset($dGeneral) && $dGeneral->telefono != null){
                        $phone = '+34'.$dGeneral->telefono;
                        enviarMensajeWhatsApp('stockaje_bajo', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo1) && $administrativo1->telefono != null){
                        $phone = '+34'.$administrativo1->telefono;
                        enviarMensajeWhatsApp('stockaje_bajo', $data, $buttondata, $phone);
                    }

                    if(isset($administrativo2) && $administrativo2->telefono != null){
                        $phone = '+34'.$administrativo2->telefono;
                        enviarMensajeWhatsApp('stockaje_bajo', $data, $buttondata, $phone);
                    }
                    
                    
                }


            }
            if ($producto) {
                $productos[] = [
                    'nombre' => $producto->nombre,
                    'cantidad' => $productoPedido->unidades,
                    'precio_ud' => $productoPedido->precio_ud,
                    'precio_total' => $productoPedido->precio_total,
                    'iva' => $producto->iva,
                    'productos_caja' => isset($producto->unidades_por_caja) ? $producto->unidades_por_caja : null,
                    'lote_id' => $stockEntrante->orden_numero,
                    'peso_kg' => ($producto->peso_neto_unidad * $productoPedido->unidades) /1000 ,
                ];
            }
        }

    }


    public function GenerarAlbaran()
    {
        $pedido = Pedido::find($this->identificador);
        if (!$pedido) {
            abort(404, 'Pedido no encontrado');
        }
       
            // Verificar que todos los productos tienen un lote_id asignado
        foreach ($this->productos_pedido as $productoPedido) {


            if (empty($productoPedido['lote_id']) && !$productoPedido['is_pack']) {
                $this->alert('error', 'Todos los productos deben tener un lote asignado.', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Aceptar',
                ]);
                return;
            }else if(!empty($productoPedido['lote_id']) && !$productoPedido['is_pack']){
                DB::table('productos_pedido')
                ->where('pedido_id',$this->identificador)
                ->where('producto_pedido_id',$productoPedido['producto_pedido_id'])
                ->update(['lote_id' => $productoPedido['lote_id']
                ]);
            }

            if($productoPedido['is_pack']){
                //comprobar que todos los productos asociados tienen lote_id
                if(collect($productoPedido['productos_asociados'])->every(fn($p) => !is_null($p['lote_id']))){
                    //todos los productos asociados tienen lote_id
                }else{
                    $this->alert('error', 'Todos los productos asociados deben tener un lote asignado.', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'confirmButtonText' => 'Aceptar',
                    ]);
                }
            }
        }
        $productosMarketingPedidos = ProductosMarketingPedido::where('pedido_id', $this->identificador)->get();
        foreach ($productosMarketingPedidos as $productoMarketingPedido) {
            $productoMarketing = ProductosMarketing::find($productoMarketingPedido->producto_marketing_id);
            $subAlmacenMarketing = Subalmacenes::where('almacen_id', $this->pedido_almacen_id)
                                        ->first();
    
            // Verificar el stock disponible en el subalmacén de marketing
            $hasStock = $this->HasStockMarketing($productoMarketingPedido, $productoMarketing, $productoMarketingPedido->unidades);
    
            if (!$hasStock) {
                $this->alert('error', '¡No hay suficiente stock de Marketing en almacén para este pedido!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Aceptar',
                ]);
                return; // Detener el proceso si no hay stock suficiente
            }
    
            // Registrar la salida de stock de productos de marketing
            $this->registrarSalidaDeStockMarketing($subAlmacenMarketing, $productoMarketingPedido->unidades, $pedido, $productoMarketing);
        }

        $cliente = Clients::find($pedido->cliente_id);
        $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
        //dd($this->productos_pedido);
        // Preparar los datos de los productos del pedido
        $productos = [];
        $hasStockRegistro = StockRegistro::where('pedido_id' , $this->pedido_id)->first();
        if (!$hasStockRegistro) {
            foreach ($this->productos_pedido as $productoPedido) {
                //dd($productoPedido['producto_pedido_id']);
                $producto = Productos::find($productoPedido['producto_pedido_id']);
                $stockSeguridad = $producto->stock_seguridad;
                $stockEntrante = StockEntrante::where('id', $productoPedido['lote_id'])->first();


        
                if (!isset($stockEntrante)) {
                    $stockEntrante = StockEntrante::where('lote_id', $productoPedido['lote_id'])->first();
                }

                // Calcula el número de cajas y pallets
                $cajas = floor($productoPedido['unidades_old'] / $producto->unidades_por_caja);
                $pallets = floor($cajas / $producto->cajas_por_pallet);
                $cajasSobrantes = $cajas % $producto->cajas_por_pallet;
                $pesoTotalProducto = ($productoPedido['unidades_old'] * $producto->peso_neto_unidad) / 1000; // Peso en kg

                //el peso total de los packs depende de los productos asociados y sus unidades
                if($productoPedido['is_pack']){
                    $pesoTotalProducto = 0;
                    foreach($productoPedido['productos_asociados'] as $productoAsociado){
                        $productoAsociadoModel = Productos::find($productoAsociado['id']);
                        $pesoTotalProducto += ($productoAsociado['unidades'] * $productoAsociadoModel->peso_neto_unidad) / 1000;
                    }
                }



                // Añadir el producto al array de productos
                $productos[] = [
                    'nombre' => $producto->nombre,
                    'lote_id' => $productoPedido['lote_id'],
                    'num_pallet' => $pallets,
                    'num_cajas' => $cajasSobrantes,
                    'cantidad' => $productoPedido['unidades_old'],
                    'peso_kg' => $pesoTotalProducto,
                ];
        
                $stockRegistro = StockRegistro::where('stock_entrante_id', $stockEntrante->id)->sum('cantidad');
                $almacen_id = Stock::find($stockEntrante->stock_id)->almacen_id;
                $almacen = Almacen::find($almacen_id);
        
                $cantidadStockDisponible = $stockEntrante->cantidad - $stockRegistro;
                
                $cantidadRestante = $productoPedido['unidades_old'];
                
        
                // Array para guardar los IDs de los lotes ya utilizados
                $arrStockDescartados = [];
                array_push($arrStockDescartados, $stockEntrante->id);
        
                

                // Primero, intenta usar el stockEntrante inicial
                if ($cantidadStockDisponible >= $cantidadRestante) {
                    // Suficiente stock en este lote para completar el pedido
                    $cantidad = $cantidadRestante;
                    $this->registrarSalidaDeStock($stockEntrante, $cantidad, $pedido, $producto, $almacen);
                    $cantidadRestante = 0; // Pedido completado
                } 
               
            }
        }else{
            foreach ($this->productos_pedido as $productoPedido) {
                $producto = Productos::find($productoPedido['producto_pedido_id']);
                // Calcula el número de cajas y pallets
                $cajas = floor($productoPedido['unidades_old'] / $producto->unidades_por_caja);
                $pallets = floor($cajas / $producto->cajas_por_pallet);
                $cajasSobrantes = $cajas % $producto->cajas_por_pallet;
                $pesoTotalProducto = ($productoPedido['unidades_old'] * $producto->peso_neto_unidad) / 1000; // Peso en kg

                if($productoPedido['is_pack']){
                    $pesoTotalProducto = 0;
                    foreach($productoPedido['productos_asociados'] as $productoAsociado){
                        $productoAsociadoModel = Productos::find($productoAsociado['id']);
                        $pesoTotalProducto += ($productoAsociado['unidades'] * $productoAsociadoModel->peso_neto_unidad) / 1000;
                    }
                }

                // Añadir el producto al array de productos

                $productos[] = [
                    'nombre' => $producto->nombre,
                    'lote_id' => $productoPedido['lote_id'],
                    'num_pallet' => $pallets,
                    'num_cajas' => $cajasSobrantes,
                    'cantidad' => $productoPedido['unidades_old'],
                    'peso_kg' => $pesoTotalProducto,
                ];

            }
        }
        

        $num_albaran = Albaran::count() + 1;
        $fecha_albaran = Carbon::now()->format('Y-m-d');
        $configuracion = Configuracion::where('id', 1)->first();
        //dd($productos);
        $datos = [
            'pedido' => $pedido,
            'cliente' => $cliente,
            'observaciones' => $pedido->observaciones,
            'productos' => $productos,
            'num_albaran' => $num_albaran,
            'fecha_albaran' => $fecha_albaran,
            'configuracion' => $configuracion,
            'productosMarketing' => $productosMarketingPedidos
        ];

        // Crear una instancia del modelo Albaran
        $albaran = new Albaran();
        $albaran->pedido_id = $pedido->id;
        $albaran->num_albaran = $num_albaran;
        $albaran->fecha = $fecha_albaran;
        $albaran ->observaciones = $pedido->observaciones;
        $albaran ->total_factura = $pedido->precio;
        // ... otros campos del albarán ...
        $albaranSave = $albaran->save(); // Guardar el albarán en la base de datos
        $pedidosSave = $pedido->update(['estado' => 4]);
        if ($pedidosSave && $albaranSave) {
            Alertas::create([
                'user_id' => 13,
                'stage' => 3,
                'titulo' => 'Estado del Pedido: Albarán',
                'descripcion' => 'Generado Albarán del pedido nº ' . $pedido->id,
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
                
                enviarMensajeWhatsApp('pedido_albaran', $data, $buttondata, $phone);
            }

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('pedido_albaran', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('pedido_albaran', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('pedido_albaran', $data, $buttondata, $phone);
            }

            if(isset($almacenAlgeciras) && $almacenAlgeciras->telefono != null && $this->pedido_almacen_id == 1){
                $phone = '+34'.$almacenAlgeciras->telefono;
                enviarMensajeWhatsApp('pedido_albaran', $data, $buttondata, $phone);
            }

            if(isset($almacenCordoba) && $almacenCordoba->telefono != null && $this->pedido_almacen_id == 2){
                $phone = '+34'.$almacenCordoba->telefono;
                enviarMensajeWhatsApp('pedido_albaran', $data, $buttondata, $phone);
            }


            $this->alert('success', '¡Albarán Generado!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido generar el albarán!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }
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
            "albaran_{$num_albaran}.pdf"
        );
    }

    public function registrarSalidaDeStock($stockEntrante, $cantidad, $pedido, $producto, $almacen) {
        // Verificar si ya existe un registro de salida para este pedido y lote
        $existingRegistro = StockRegistro::where('stock_entrante_id', $stockEntrante->id)
            ->where('pedido_id', $pedido->id)
            ->where('motivo', 'Salida')
            ->where('tipo', 'Venta')
            ->first();
    
        if ($existingRegistro) {
            // Actualizar el registro existente
            $existingRegistro->cantidad += $cantidad;
            if ($existingRegistro->cantidad < 0) {
                $existingRegistro->cantidad = 0; // Evitar cantidades negativas
            }
            $existingRegistro->save();
        } else {
            // Crear un nuevo registro de salida
            $stockRegistro = new StockRegistro();
            $stockRegistro->stock_entrante_id = $stockEntrante->id;
            $stockRegistro->cantidad = $cantidad;
            $stockRegistro->tipo = "Venta";
            $stockRegistro->motivo = "Salida";
            $stockRegistro->pedido_id = $pedido->id;
            $stockRegistro->save();
        }
    
        $existingSaliente = StockSaliente::where('stock_entrante_id', $stockEntrante->id)
            ->where('pedido_id', $pedido->id)
            ->first();
    
        if ($existingSaliente) {
            // Actualizar el registro existente
            $existingSaliente->cantidad_salida += $cantidad;
            if ($existingSaliente->cantidad_salida < 0) {
                $existingSaliente->cantidad_salida = 0; // Evitar cantidades negativas
            }
            $existingSaliente->save();
        } else {
            // Crear un nuevo registro de salida
            StockSaliente::create([
                'stock_entrante_id' => $stockEntrante->id,
                'producto_id' => $producto->id,
                'cantidad_salida' => $cantidad,
                'fecha_salida' => Carbon::now(),
                'pedido_id' => $pedido->id,
                'tipo' => 'Pedido',
                'motivo_salida' => 'Venta',
                'almacen_origen_id' => $almacen->id,
            ]);
        }
    }
    

    //tras escanear el qr
    public function handleQrScanned($qrCode, $rowIndex)
    {


        if($this->productos_pedido[$rowIndex]['is_pack']){
            //dd("hola");
            //coger los pedidos asociados y asociarles un lote
            $productosAsociados = $this->productos_pedido[$rowIndex]['productos_asociados'];
            $text = '';
            foreach($productosAsociados as $index => $productoAsociado){
                //dd($productoAsociado['nombre']);
                if($productoAsociado['lote_id'] != null || $productoAsociado['lote_id'] != ''){
                    //dd("hola");
                    continue;
                }
                $stocksEntrantes = StockEntrante::where('producto_id', $productoAsociado['id'])->get();
                $stocks = [];
                
                //filtrar de esos stockEntrantes cuales pertenecen a este almacen, para ello debemos mirar en stock que este relacionado con este stockEntrante
                foreach($stocksEntrantes as $stockEntrante){
                    $stock = Stock::where('id', $stockEntrante->stock_id)->first();
                    if($stock->almacen_id == $this->pedido_almacen_id){
                        $stocks[] = $stockEntrante;
                    }
                }

                //dd($stocks);
                if(count($stocks) == 0){
                    //dd("hola");
                    //crear un text para despues generar alerta
                    $text = $text . "No hay stock disponible para este producto: " . $productoAsociado['nombre'] . ". <br>";
                    continue;
                }
                //dd($stocks);
                //filtrar de esos stocks cuales tienen stock suficiente
                $stocksValidos = [];
                foreach($stocks as $stock){
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $stock->id)->sum('cantidad');
                    //dd($stockRegistro);
                    //dd($stock);
                    $cantidadStock = $stock->cantidad - $stockRegistro;
                    //dd($cantidadStock);
                    if($cantidadStock >= $productoAsociado['unidades']){
                        $stocksValidos[] = $stock;
                    }
                }
                if(count($stocksValidos) == 0){
                    $this->alert('error', 'No hay stock disponible para este producto.', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                    ]);
                    $text = $text . "No hay stock disponible para este producto: " . $productoAsociado['nombre'] . ". <br>";
                    continue;
                }

                //dd($stocksValidos);
                //filtrar de esos stocksValidos cuales tienen la cantidad de stock necesaria
                $stocksValidosConStockSuficiente = [];
                foreach($stocksValidos as $stock){
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $stock->id)->sum('cantidad');
                    $cantidadStock = $stock->cantidad - $stockRegistro;
                    if($cantidadStock >= $productoAsociado['unidades']){
                        $stocksValidosConStockSuficiente[] = $stock;
                    }
                }
                if(count($stocksValidosConStockSuficiente) == 0){
                    $text = $text . "No hay stock disponible para este producto: " . $productoAsociado['nombre'] . ". <br>";
                    continue;
                }
                //dd($stocksValidosConStockSuficiente);

                //de los stocksValidosConStockSuficiente cogemos el mas antiguo dependiendo de su created_at
                $stockMasAntiguo = null;
                foreach($stocksValidosConStockSuficiente as $stock){
                    if(!isset($stockMasAntiguo) || $stock->created_at < $stockMasAntiguo->created_at){
                        $stockMasAntiguo = $stock;
                    }
                
                
                }
                if(!isset($stockMasAntiguo)){
                    $text = $text . "No hay stock disponible para este producto: " . $productoAsociado['nombre'] . ". <br>";
                    continue;
                }
                //dd($stockMasAntiguo);
               // dd($stockMasAntiguo);

                if($stockMasAntiguo){
                    $this->productos_pedido[$rowIndex]['productos_asociados'][$index]['lote_id'] = $stockMasAntiguo->lote_id;
                    //restar la cantidad en el registro de stock
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $stockMasAntiguo->id)->sum('cantidad');

                    if($productoAsociado['unidades'] > 0){
                        StockRegistro::create([
                            'stock_entrante_id' => $stockMasAntiguo->id,
                            'cantidad' => $productoAsociado['unidades'],
                            'motivo' => 'Salida',
                            'tipo' => 'Venta',
                            'pedido_id' => $this->pedido_id,

                        ]);

                        StockSaliente::create([
                            'stock_entrante_id' => $stockMasAntiguo->id,
                            'producto_id' => $productoAsociado['id'],
                            'cantidad_salida' => $productoAsociado['unidades'],
                            'fecha_salida' => Carbon::now(),
                            'pedido_id' => $this->pedido_id,
                            'tipo' => 'Pedido',
                            'almacen_origen_id' => $this->pedido_almacen_id,
                        ]);


                    }
                        

                    ProductosPedidoPack::where('pack_id', $this->productos_pedido[$rowIndex]['producto_pedido_id'])->where('producto_id', $productoAsociado['id'])->where('pedido_id', $this->pedido_id)->update([
                        'lote_id' => $stockMasAntiguo->lote_id
                    ]); 
                    
                }

   

            }
            //dd($text);
            if($text != ''){
                $this->alert('error', $text, [
                    'position' => 'center',
                    'timer' => false,
                    'toast' => false,
                ]);
                
            }else{
                $this->alert('success', 'Lotes asignados correctamente.', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Aceptar',
                ]);
            }
            return;

        }

        //dd("hola");
        if($this->pedido_almacen_id == null){
            $this->alert('error', 'No tienes un almacén asignado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return;
        }


        $stock = Stock::where('qr_id', $qrCode)->first();
        if (!$stock) {
            $this->alert('error', 'QR no asignado o inválido.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return;
        }

        if($stock->almacen_id != $this->pedido_almacen_id){
            $this->alert('error', 'El QR escaneado no pertenece a tu almacén.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return;
        }
        
        $entradaStock = StockEntrante::where('stock_id', $stock->id)->first();
        if (!$entradaStock) {
            $this->alert('error', 'Lote no encontrado.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return;
        }

            //entrada stock producto_id es distinto a productos pedido producto_pedido_id
        if ($entradaStock->producto_id != $this->productos_pedido[$rowIndex]['producto_pedido_id']) {
            $this->alert('error', 'El QR escaneado no corresponde al producto requerido.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
            return;
        }

        $cantidadStock = 0;
        $stockRegistro = StockRegistro::where('stock_entrante_id', $entradaStock->id)->sum('cantidad');
        $cantidadStock = $entradaStock->cantidad - $stockRegistro;
        //dd($cantidadStock >= $this->productos_pedido[$rowIndex]['unidades_old']);
        // Si el stock del lote es suficiente
        if ($cantidadStock >= $this->productos_pedido[$rowIndex]['unidades_old']) {
            $this->productos_pedido[$rowIndex]['lote_id'] = $entradaStock->id;
            $this->alert('success', 'Lote asignado correctamente.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
        } else {
            // Si no es suficiente, dividir la cantidad necesaria entre los lotes disponibles
            $this->productos_pedido[$rowIndex]['unidades_old'] -= $cantidadStock; // Restar la cantidad que puede proporcionar este lote
            $this->productos_pedido[] = [
                'producto_pedido_id' => $this->productos_pedido[$rowIndex]['producto_pedido_id'],
                'unidades' => 0, // Asignar lo que este lote puede dar
                'unidades_old' => $cantidadStock, // Asignar lo que este lote puede dar
                'precio_ud' => $this->productos_pedido[$rowIndex]['precio_ud'],
                'precio_total' => $cantidadStock * $this->productos_pedido[$rowIndex]['precio_ud'],
                'lote_id' => $entradaStock->id
            ];

        

            $this->alert('warning', 'Cantidad insuficiente en este lote, por favor escanea otro lote para completar la cantidad.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'Aceptar',
            ]);
        }
    }
}
