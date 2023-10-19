<?php

namespace App\Http\Livewire\Iva;

use App\Models\Iva;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $name;
    public $iva;

    public function render()
    {
        return view('livewire.iva.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'name' => 'required',
            'iva' => 'required',
        ],
            // Mensajes de error
            [
                'name.required' => 'El nombre es obligatorio.',
                'iva.required' => 'El iva es obligatorio.',
            ]);

        // Guardar datos validados
        $productosSave = Iva::create($validatedData);

        // Alertas de guardado exitoso
        if ($productosSave) {
            $this->alert('success', 'IVA registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del producto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('iva.index');

    }
}
