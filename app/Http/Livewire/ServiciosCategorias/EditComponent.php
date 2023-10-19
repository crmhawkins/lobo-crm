<?php

namespace App\Http\Livewire\ServiciosCategorias;

use App\Models\ServicioCategoria;
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
        $servicio = ServicioCategoria::find($this->identificador);

        $this->nombre = $servicio->nombre;

    }

    public function render()
    {
        return view('livewire.servicios_categorias.edit-component');
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
        $servicioCategoria = ServicioCategoria::find($this->identificador);

        // Guardar datos validados
        $servicioSave = $servicioCategoria->update([
            'nombre' => $this->nombre
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 33, $servicioCategoria->id));

        if ($servicioSave) {
            $this->alert('success', 'Usuario actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Servicio actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar la categoría? No hay vuelta atrás', [
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
        return redirect()->route('servicios-categorias.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $servicio = ServicioCategoria::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 34, $servicio->id));
        $servicio->delete();
        return redirect()->route('servicios-categorias.index');

    }
}
