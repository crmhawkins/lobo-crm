<?php

namespace App\Http\Livewire\Caja;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\Presupuesto;
use App\Models\TipoEvento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
	    use LivewireAlert;

    public $identificador;

    public $tipo_movimiento;
    public $metodo_pago;
    public $importe;
    public $descripcion;
    public $presupuesto_id;
    public $fecha;
    public $clientes;
    public $categorias;
    public $presupuestos;
    public $eventos;



    public function mount()
    {
        $caja = Caja::find($this->identificador);
        $this->presupuestos = Presupuesto::all();
        $this->categorias = TipoEvento::all();
        $this->eventos = Evento::all();
        $this->clientes = Cliente::all();
        $this->metodo_pago = $caja->metodo_pago;
        $this->descripcion = $caja->descripcion;
        $this->importe = $caja->importe;
        $this->presupuesto_id = $caja->presupuesto_id;
        $this->fecha = $caja->fecha;
        $this->tipo_movimiento = $caja->tipo_movimiento;


    }

    public function render()
    {
        return view('livewire.caja.edit-component');
    }

// Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'metodo_pago' => 'required',
            'importe' => 'required',
            'presupuesto_id' => 'required',
            'fecha' => 'required',
            'tipo_movimiento' => 'required',
            'descripcion' => 'required',

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Encuentra el identificador
        $caja = Caja::find($this->identificador);

        // Guardar datos validados
        $tipoSave = $caja->update([
            'metodo_pago' => $this->metodo_pago,
            'importe' => $this->importe,
            'presupuesto_id' => $this->presupuesto_id,
            'fecha' => $this->fecha,
            'tipo_movimiento' => $this->tipo_movimiento,
            'descripcion' => $this->descripcion

        ]);
        event(new \App\Events\LogEvent(Auth::user(), 53, $tipoSave->id));

        if ($tipoSave) {
            $this->alert('success', '¡Movimiento de caja actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del movimiento de caja!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Movimiento de caja actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el movimiento de caja? No hay vuelta atrás', [
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
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('tipo-evento.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $tipo_evento = TipoEvento::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 54, $tipo_evento->id));
        $tipo_evento->delete();
        return redirect()->route('tipo-evento.index');

    }
}
