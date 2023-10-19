<?php

namespace App\Http\Livewire\ServiciosCategorias;

use App\Models\ServicioCategoria;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;


    public function mount(){
    }

    public function render()
    {
        return view('livewire.servicios_categorias.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'precioBase.required' => 'El precio base son obligatorio.',
                'pack.required' => 'El campo pack es obligatoria.',
                'categoria.required' => 'El nombre de usuario es obligatorio.',
            ]);

        // Guardar datos validados
        $usuariosSave = ServicioCategoria::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 32, $usuariosSave->id));

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', 'Servicio registrado correctamente!', [
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
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('servicios-categorias.index');

    }
}
