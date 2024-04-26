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
        $this->precio = $this->facturas->precio;
        $this->pedido_id = $this->facturas->pedido_id;
        $this->numero_factura = $this->facturas->numero_factura;
        $this->fecha_emision = $this->facturas->fecha_emision;
        $this->fecha_vencimiento = $this->facturas->fecha_vencimiento;
        $this->descripcion = $this->facturas->descripcion;
        $this->estado = $this->facturas->estado;
        $this->metodo_pago = $this->facturas->metodo_pago;
        if(!$this->facturas->descuento){
            if($this->pedido->descuento){
                $this->descuento = $this->pedido->porcentaje_descuento;
            }
        }else{
            $this->descuento = $this->facturas->descuento;
        }
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
                'metodo_pago' => 'nullable',
                'producto_id' => 'nullable',
                'cantidad' => 'nullable'
            ],
            // Mensajes de error
            [
                'numero_factura.required' => 'Indique un nº de factura.',
                'fecha_emision.required' => 'Ingrese una fecha de emisión',

            ]
        );

        // Guardar datos validados
        $facturasSave = $this->facturas->update([
            'numero_factura' => $this->numero_factura,
            'cliente_id' => $this->cliente_id,
            'pedido_id'  => $this->pedido_id,
            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'precio' => $this->precio,
            'metodo_pago' => $this->metodo_pago,
            'cantidad' => $this->cantidad,
            'producto_id' =>$this->producto_id,
            'descuento' =>$this->descuento,

        ]);

        if($this->facturas->estado == "Pagado"){
            $pedido=Pedido::find($this->pedido_id);
            if (isset($this->pedido_id) && isset($pedido)){
                 $pedido->update(['estado' => 6]);
                }
        }

        if ($facturasSave) {
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

        session()->flash('message', 'Factura actualizada correctamente.');

        $this->emit('productUpdated');
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
            'listarPresupuesto'
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
        event(new \App\Events\LogEvent(Auth::user(), 19, $factura->id));
        $factura->delete();
        return redirect()->route('facturas.index');
    }

    public function imprimirFacturaIva()
    {

        $factura = Facturas::find($this->identificador);

        if($factura != null){
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran :: where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact= Productos::find($factura->producto_id);
            $productos = [];
            if(isset($pedido)){
                $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

                // Preparar los datos de los productos del pedido
                foreach ($productosPedido as $productoPedido) {
                    $producto = Productos::find($productoPedido->producto_pedido_id);
                    if ($producto) {
                        $productos[] = [
                            'nombre' => $producto->nombre,
                            'cantidad' => $productoPedido->unidades,
                            'precio_ud' => $productoPedido->precio_ud,
                            'precio_total' => $productoPedido->precio_total,
                            'iva' => $producto->iva,
                            'lote_id' => $productoPedido->lote_id,
                            'peso_kg' =>($producto->peso_neto_unidad * $productoPedido->unidades) /1000,
                        ];
                    }
                }}
            $iva=true;

            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'productos' => $productos,
                'producto' => $productofact,
            ];

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos)->setPaper('a4', 'vertical')->output();
        Mail::to($cliente->email)->send(new FacturaMail($pdf, $datos));

        /*return response()->streamDownload(
            fn () => print($pdf->output()),
            "factura_{$factura->id}.pdf");*/
        }else{
            return redirect('admin/facturas');
        }


    }
    public function imprimirFactura()
    {

        $factura = Facturas::find($this->identificador);

        if($factura != null){
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran :: where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($factura->cliente_id);
            $productofact= Productos::find($factura->producto_id);
            $productos = [];
            if(isset($pedido)){
                $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

                // Preparar los datos de los productos del pedido
                foreach ($productosPedido as $productoPedido) {
                    $producto = Productos::find($productoPedido->producto_pedido_id);
                    if ($producto) {
                        $productos[] = [
                            'nombre' => $producto->nombre,
                            'cantidad' => $productoPedido->unidades,
                            'precio_ud' => $productoPedido->precio_ud,
                            'precio_total' => $productoPedido->precio_total,
                            'iva' => $producto->iva,
                            'lote_id' => $productoPedido->lote_id,
                            'peso_kg' => ($producto->peso_neto_unidad * $productoPedido->unidades) /1000,
                        ];
                    }
                }}
            $iva=false;

            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'productos' => $productos,
                'producto' => $productofact,
            ];

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos)->setPaper('a4', 'vertical')->output();
        Mail::to($cliente->email)->send(new FacturaMail($pdf, $datos));

        /*return response()->streamDownload(
            fn () => print($pdf->output()),
            "factura_{$factura->id}.pdf");*/
        }else{
            return redirect('admin/facturas');
        }
    }

}
