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

    public $alumnosSinEmpresa;
    public $alumnosConEmpresa;
    public $cursos;
    public $presupuestos;
    public $facturas;

    public $pedido;
    public $pedido_id;


    public function mount()
    {
        $this->facturas = Facturas::find($this->identificador);
        $this->pedido = Pedido::find($this->facturas->pedido_id);
        $this->pedido_id = $this->facturas->pedido_id;
        $this->numero_factura = $this->facturas->numero_factura;
        $this->fecha_emision = $this->facturas->fecha_emision;
        $this->fecha_vencimiento = $this->facturas->fecha_vencimiento;
        $this->descripcion = $this->facturas->descripcion;
        $this->estado = $this->facturas->estado;
        $this->metodo_pago = $this->facturas->metodo_pago;



    }

    public function render()
    {

        // $this->tipoCliente == 0;
        return view('livewire.facturas.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate(
            [
                'numero_factura' => 'required',
                'fecha_emision' => 'required',
                'fecha_vencimiento' => '',
                'descripcion' => '',
                'estado' => 'required',
                'metodo_pago' => '',
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

            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'metodo_pago' => $this->metodo_pago,

        ]);

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
                        'iva' => $producto->iva,
                        'lote_id' => $productoPedido->lote_id,
                        'peso_kg' => 1000 / $producto->peso_neto_unidad * $productoPedido->unidades,
                    ];
                }
            }
            $iva=true;
            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
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
                        'iva' => $producto->iva,
                        'lote_id' => $productoPedido->lote_id,
                        'peso_kg' => 1000 / $producto->peso_neto_unidad * $productoPedido->unidades,
                    ];
                }
            }
            $iva=false;
            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
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
