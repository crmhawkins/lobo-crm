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

use App\Models\ProductosFacturas;
use App\Models\StockEntrante;
use App\Models\StockRegistro;

class CreateRectificativa extends Component
{
    public function render()
    {
        return view('livewire.facturas.create-rectificativa');
    }
    
    use LivewireAlert;

    public $idpedido;
    public $numero_factura; // 0 por defecto por si no se selecciona ninguno
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descripcion;
    public $estado = "Pendiente";
    public $metodo_pago = "No Pagado";
    public $cliente;
    public $clientes;
    public $cliente_id;
    public $pedido;
    public $precio;
    public $pedido_id;
    public $producto_id;
    public $productos;
    public $cantidad;
    public $descuento;
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

    public function mount()
    {   
   
        $this->facturas = Facturas::all();
        $this->productos = Productos::all();
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

        if($year = 24){
            if($lastnumber == 30){
                $lastNumber = 31;
            }
        }

        $this->clientes = Clients::where('estado', 2)->get();

        $this->numero_factura = 'CN' . $year . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
        $this->fecha_emision = Carbon::now()->format('Y-m-d');

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

            $this->cliente_id = $this->pedido->cliente_id;
            $this->cliente = Clients::find($this->cliente_id);

            //productos_pedido to array
            $this->productos_pedido = json_decode(json_encode($this->productos_pedido), true);
            $this->subtotal_pedido = $this->pedido->subtotal;
            
            $this->iva_total_pedido = $this->pedido->iva_total;
            $this->descuento_total_pedido = $this->pedido->descuento_total;
            $this->total = $this->pedido->precio + $this->pedido->iva_total;
            $this->precio = $this->pedido->precio;
            $this->descuento = $this->pedido->descuento ? $this->pedido->porcentaje_descuento : 0;
              
            $this->total = number_format($this->total, 2, '.', '');
        }

        
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

    public function getNombreTabla($id)
    {
        $nombre_producto = $this->productos->where('id', $id)->first()->nombre;
        return $nombre_producto;
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

 // Función para cuando se llama a la alerta
 public function getListeners()
 {
     return [
         'confirmed',
         'submit',
         'destroy',
         'listarPedido',
     ];
 }

  // Función para cuando se llama a la alerta
  public function confirmed()
  {
      // Do something
      return redirect()->route('facturas.index');
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

    // Al hacer submit en el formulario
    public function submit()
    {
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
                    'subtotal_pedido' => 'nullable',
                    'iva_total_pedido' => 'nullable',
                    'descuento_total_pedido' => 'nullable',
                    'total' => 'nullable',

                    
                ],
                // Mensajes de error
                [
                    'numero_factura.required' => 'Indique un nº de factura.',
                    'fecha_emision.required' => 'Ingrese una fecha de emisión',
                    'id_pedido.min' => 'Seleccione un pedido',
                ]
            );
        
        
        // Guardar datos validados
        $facturasSave = Facturas::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));
        $pedidosSave =false;
        // Alertas de guardado exitoso
            
        if ($facturasSave) {
            //$this->calcularTotales($facturasSave);

            $facturasSave->factura_id = $this->facturaSeleccionadaId;
            $facturasSave->save();

            $facturaNormal = Facturas::find($this->facturaSeleccionadaId);
            $facturaNormal->factura_rectificativa_id = $facturasSave->id;
            $facturaNormal->save(); 

            if(count($this->productos_pedido) > 0){
                foreach ($this->productos_pedido as $producto) {
                    //dd($this->productos_pedido[1] , StockEntrante::where('id', $this->productos_pedido[1]['lote_id'])->first());

                    if(isset($producto['descontar_ud'])){
                        
                        //buscamos stock entrante que coincida con product id y lote id
                        $stockEntrante = StockEntrante::where('id', $producto['lote_id'])->first();

                       
                        //dd($stockEntrante);
                        //si encontramos, le sumamos las unidades a descontar que son las unidades a rectificar
                        if($stockEntrante){

                            $stockEntranteRegistro = new StockRegistro();
                            $stockEntranteRegistro->stock_entrante_id = $stockEntrante->id;
                            $stockEntranteRegistro->cantidad = -$producto['descontar_ud'];
                            $stockEntranteRegistro->tipo = 'devolucion';
                            $stockEntranteRegistro->factura_id = $facturasSave->id;
                            $stockEntranteRegistro->motivo = 'Entrada';
                            $stockEntranteRegistro->save();
                            
                            //creamos un productos_factura con las unidades a descontar
                            $productosFactura = new ProductosFacturas();
                            $productosFactura->factura_id = $facturasSave->id;
                            $productosFactura->producto_id = $producto['producto_pedido_id'];
                            $productosFactura->cantidad = $producto['descontar_ud'];
                            $productosFactura->unidades = $producto['unidades'];
                            $productosFactura->precio_ud = $producto['precio_ud'];
                            $total = $producto['precio_total'];
                            $productosFactura->total = $total;
                            $productosFactura->stock_entrante_id = $stockEntrante->id;
                            $productosFactura->save();
                        }

                    }
                }
            }

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
}
