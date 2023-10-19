<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Presupuesto;
use App\Models\Cursos;
use App\Models\Alumno;
use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\Empresa;
use App\Models\Evento;
use App\Models\ServicioPack;
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


    public $numero_factura;
    public $id_presupuesto;
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

    public $estadoPresupuesto;
    public $presupuestoSeleccionado;
    public $alumnoDePresupuestoSeleccionado;
    public $cursoDePresupuestoSeleccionado;


    public function mount()
    {
        $this->facturas = Facturas::find($this->identificador);

        $this->presupuestos = Presupuesto::all();

        $this->numero_factura = $this->facturas->numero_factura;
        $this->id_presupuesto = $this->facturas->id_presupuesto;
        $this->fecha_emision = $this->facturas->fecha_emision;
        $this->fecha_vencimiento = $this->facturas->fecha_vencimiento;
        $this->descripcion = $this->facturas->descripcion;
        $this->estado = $this->facturas->estado;
        $this->metodo_pago = $this->facturas->metodo_pago;


        if ($this->id_presupuesto > 0 || $this->id_presupuesto != null) {
            $this->listarPresupuesto($this->id_presupuesto);
        }
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
                'id_presupuesto' => 'required|numeric|min:1',
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
                'id_presupuesto.min' => 'Seleccione un presupuesto',
            ]
        );

        // Guardar datos validados
        $facturasSave = $this->facturas->update([
            'numero_factura' => $this->numero_factura,
            'id_presupuesto' => $this->id_presupuesto,
            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'metodo_pago' => $this->metodo_pago,

        ]);
        event(new \App\Events\LogEvent(Auth::user(), 18, $factura->id));

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
