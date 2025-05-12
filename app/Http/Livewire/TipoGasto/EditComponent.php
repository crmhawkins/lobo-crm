<?php

namespace App\Http\Livewire\TipoGasto;


use App\Models\TipoGasto;
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
        $tipo_gasto = TipoGasto::find($this->identificador);

        $this->nombre = $tipo_gasto->nombre;

    }
    public function render()
    {
        return view('livewire.tipo-gasto.edit-component');
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
        $tipo_gasto = TipoGasto::find($this->identificador);

        // Guardar datos validados
        $tipoSave = $tipo_gasto->update([
            'nombre' => $this->nombre
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 50, $tipo_gasto->id));

        if ($tipoSave) {
            $this->alert('success', '¡Tipo de gasto actualizado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del tipo de gasto!', [
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

        $this->alert('warning', '¿Seguro que desea borrar el tipo de gasto? No hay vuelta atrás', [
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
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('tipo-gasto.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $tipo_gasto = TipoGasto::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 51, $tipo_gasto->id));
        $tipo_gasto->delete();
        return redirect()->route('tipo-gasto.index');

    }
}
