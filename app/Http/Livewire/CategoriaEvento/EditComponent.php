<?php

namespace App\Http\Livewire\CategoriaEvento;

use App\Models\CategoriaEvento;
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
        $categoria_evento = CategoriaEvento::find($this->identificador);

        $this->nombre = $categoria_evento->nombre;

    }
	
    public function render()
    {
        return view('livewire.categoria-evento.edit-component');
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
        $categoria_evento = CategoriaEvento::find($this->identificador);

        // Guardar datos validados
        $tipoSave = $categoria_evento->update([
            'nombre' => $this->nombre
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 50, $categoria_evento->id));

        if ($tipoSave) {
            $this->alert('success', '¡Categoría de evento actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del Categoría de evento!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Categoría de evento actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el Categoría de evento? No hay vuelta atrás', [
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
        return redirect()->route('categoria-evento.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $categoria_evento = CategoriaEvento::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 57, $categoria_evento->id));
        $categoria_evento->delete();
        return redirect()->route('categoria-evento.index');

    }
	
}
