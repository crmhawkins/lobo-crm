<?php

namespace App\Http\Livewire\TipoEvento;

use App\Models\TipoEvento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
	    use LivewireAlert;

    public $identificador;

    public $nombre;

	
    public function mount()
    {
        $tipo_evento = TipoEvento::find($this->identificador);

        $this->nombre = $tipo_evento->nombre;

    }
	
    public function render()
    {
        return view('livewire.tipo-evento.edit-component');
    }

// Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'nombre' => 'required',
        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Encuentra el identificador
        $tipo_evento = TipoEvento::find($this->identificador);

        // Guardar datos validados
        $tipoSave = $tipo_evento->update([
            'nombre' => $this->nombre
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 53, $tipo_evento->id));

        if ($tipoSave) {
            $this->alert('success', '¡Tipo de evento actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del tipo de evento!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Tipo de gasto actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el tipo de evento? No hay vuelta atrás', [
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
