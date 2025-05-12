<?php

namespace App\Http\Livewire\Gastos;

use App\Models\Presupuesto;
use App\Models\Cursos;
use App\Models\Alumno;
use App\Models\Cliente;
use App\Models\Gastos;
use App\Models\TipoGasto;
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

    public $tipo_gasto;
    public $tipos_gasto;

    public $nombre_gasto;
    public $date;
    public $repeticion;
    public $cuantia;
    public $activo;


    public function mount()
    {
        $this->gastos = Gastos::find($this->identificador);
        $this->tipos_gasto = TipoGasto::all();
        $this->tipo_gasto = $this->gastos->tipo_gasto;
        $this->nombre_gasto = $this->gastos->nombre_gasto;
        $this->date = $this->gastos->date;
        $this->cuantia = $this->gastos->cuantia;
    }

    public function render()
    {

        // $this->tipoCliente == 0;
        return view('livewire.gastos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'tipo_gasto' => 'required',
                'nombre_gasto' => 'required',
                'date' => 'nullable',
                'cuantia' => 'required',
            ],
            // Mensajes de error
            [
                'tipo_gasto.required' => 'Indique un tipo de gasto.',
                'nombre_gasto.required' => 'Ingrese un concepto de gasto',
                'cuantia.required' => 'Indique la cuantía del gasto',
            ]
        );

        // Guardar datos validados
        $gastosSave = $this->gastos->update($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 45, $factura->id));

        if ($GastosSave) {
            $this->alert('success', '¡Gasto actualizado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del gasto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Gasto actualizado correctamente.');

        $this->emit('productUpdated');
    }

    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar el gasto? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'update',
            'destroy',
            'activarGasto',
            'desactivarGasto',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('gastos.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $factura = Gastos::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 46, $factura->id));
        $factura->delete();
        $this->confirmed();
    }

    public function activarGasto()
    {
        $factura = Gastos::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 47, $factura->id));
        $factura->update(['activo' => 1, 'date' => Carbon::now()->format('d/m/Y')]);
        $this->confirmed();
    }


    public function desactivarGasto()
    {
        $factura = Gastos::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 48, $factura->id));
        $factura->update(['activo' => 0]);
        $this->confirmed();
    }



}
