<?php

namespace App\Http\Livewire\Iva;

use App\Models\Iva;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $name;
    public $iva;

    public function mount($identificador)
    {
        $iva = Iva::find($identificador);

        $this->name = $iva->name;
        $this->iva = $iva->iva;
    }

    public function render()
    {
        return view('livewire.iva.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'name' => 'required',
            'iva' => 'required',
        ],
            // Mensajes de error
            [
                'name.required' => 'El nombre es obligatorio.',
                'iva.required' => 'El nombre es obligatorio.',
            ]);

        // Encuentra el IVA identificado
        $iva = Iva::find($this->identificador);

        // Guardar datos validados
        $ivaSave = $iva->update([
            'name' => $this->name,
            'iva' => $this->iva,
        ]);

        if ($ivaSave) {
            $this->alert('success', 'IVA actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del IVA!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'IVA editado correctamente.');

        $this->emit('productUpdated');
    }

      // Elimina el IVA
      public function destroy(){


        $this->alert('warning', '¿Seguro que desea borrar el IVA? No hay vuelta atrás', [
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
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('iva.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $iva = Iva::find($this->identificador);
        $iva->delete();
        return redirect()->route('iva.index');

    }
}
