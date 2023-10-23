<?php

namespace App\Http\Livewire\Almacen;

use App\Models\Albaran;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Presupuesto;
use App\Models\Cursos;
use App\Models\Alumno;
use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\Empresa;
use App\Models\Evento;
use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\ServicioPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $albaran;
    public $num_albaran;
    public $pedido_id;
    public $fecha;
    public $observaciones;
    public $descripcion;
    public $total_factura;

    public $alumnosSinEmpresa;
    public $productos;
    public $cursos;
    public $presupuestos;
    public $facturas;

    public $productos_pedido = [];
    public $lotes;
    public $pedido;
    public $cliente;


    public function mount()
    {
        $this->albaran = Albaran::find($this->identificador);
        $this->pedido = Pedido::where('id', $this->albaran->pedido_id)->first();
        $this->cliente = Clients::where('id', $this->pedido->cliente_id)->first();
        $this->lotes = ProductoLote::all();
        $this->productos = Productos::all();
        $this->num_albaran = $this->albaran->num_albaran;
        $this->pedido_id = $this->albaran->pedido_id;
        $this->fecha = $this->albaran->fecha;
        $this->observaciones = $this->albaran->observaciones;
        $this->descripcion = $this->albaran->descripcion;
        $this->total_factura = $this->albaran->total_factura;
        $productos = DB::table('productos_pedido')->where('pedido_id', $this->pedido_id)->get();
        foreach ($productos as $producto) {
            $this->productos_pedido[] = [
                'id' => $producto->id,
                'producto_lote_id' => $producto->producto_lote_id,
                'unidades_old' => $producto->unidades,
                'unidades' => 0,
            ];
        }
    }

    public function render()
    {

        // $this->tipoCliente == 0;
        return view('livewire.almacen.edit-component');
    }
    public function getNombreTabla($id)
    {
        $producto_id = $this->lotes->where('id', $id)->first()->producto_id;
        $nombre_producto = $this->productos->where('id', $producto_id)->first()->nombre;
        return $nombre_producto;
    }
    public function getNombreLoteTabla($id)
    {
        $producto_id = $this->lotes->where('id', $id)->first()->lote_id;
        return $producto_id;
    }
    // Al hacer update en el formulario
    public function update()
    {
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

        $pedido = Pedido::firstWhere('id', $this->pedido_id)->update(['estado' => 6]);

        // Guardar datos validados
        $facturasSave = $this->albaran->update($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 18, $this->identificador));

        if ($facturasSave) {
            $this->alert('success', '¡Pedido marcado como enviado!', [
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

        session()->flash('message', 'Pedido actualizado correctamente.');

        $this->emit('productUpdated');
    }
    public function updateCompletado()
    {
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

        $pedido = Pedido::firstWhere('id', $this->pedido_id)->update(['estado' => 7]);

        // Guardar datos validados
        $facturasSave = $this->albaran->update($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 18, $this->identificador));

        if ($facturasSave) {
            $this->alert('success', '¡Pedido marcado como enviado!', [
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

        session()->flash('message', 'Pedido actualizado correctamente.');

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
            'updateCompletado',
            'confirmDelete',
            'aceptarFactura',
            'cancelarFactura',
            'imprimirFactura',
            'listarPresupuesto'
        ];
    }
    public function aceptarFactura()
    {
        $presupuesto = $this->presupuestos->where('id', $this->facturas->id_presupuesto)->first();

        $presupuestoSave = $presupuesto->update(['estado' => 'Facturado']);

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

    public function generarDocumento()
    {
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
        return redirect()->route('almacen.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $factura = Facturas::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 19, $factura->id));
        $factura->delete();
        return redirect()->route('almacen.index');
    }

    public function listarPresupuesto($id)
    {
        $this->id_presupuesto = $id;
        if ($this->id_presupuesto != null) {
            $this->estadoPresupuesto = 1;
            $this->presupuestoSeleccionado = Presupuesto::where('id', $this->id_presupuesto)->first();
            $this->alumnoDePresupuestoSeleccionado = Alumno::where('id', $this->presupuestoSeleccionado->alumno_id)->first();
            $this->cursoDePresupuestoSeleccionado = Cursos::where('id', $this->presupuestoSeleccionado->curso_id)->first();
        } else {
            $this->estadoPresupuesto = 0;
        }
    }

    public function imprimirFactura()
    {
        $factura = Facturas::find($this->identificador);
        $presupuesto = Presupuesto::find($this->id_presupuesto);
        $cliente = Cliente::where('id', $presupuesto->id_cliente)->first();
        $evento = Evento::where('id', $presupuesto->id_evento)->get();
        $listaServicios = [];
        $listaPacks = [];
        $packs = ServicioPack::all();

        foreach ($presupuesto->servicios()->get() as $servicio) {
            $listaServicios[] = ['id' => $servicio->id, 'numero_monitores' => $servicio->pivot->numero_monitores, 'precioFinal' => $servicio->pivot->precio_final, 'existente' => 1];
        }

        foreach ($presupuesto->packs()->get() as $pack) {
            $listaPacks[] = ['id' => $pack->id, 'numero_monitores' => json_decode($pack->pivot->numero_monitores, true), 'precioFinal' => $pack->pivot->precio_final, 'existente' => 1];
        }


        $datos =  [
            'presupuesto' => $presupuesto, 'factura' => $factura, 'cliente' => $cliente,
            'evento' => $evento, 'listaServicios' => $listaServicios, 'listaPacks' => $listaPacks, 'packs' => $packs,
        ];

        $pdf = PDF::loadView('livewire.facturas.certificado-component', $datos)->setPaper('a4', 'vertical')->output(); //
        return response()->streamDownload(
            fn () => print($pdf),
            'export_protocol.pdf'
        );
    }
}
