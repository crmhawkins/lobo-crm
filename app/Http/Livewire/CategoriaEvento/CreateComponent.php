<?php

namespace App\Http\Livewire\CategoriaEvento;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\CategoriaEvento;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{
	 use LivewireAlert;

    public $nombre;
	
	  public function mount(){
    }
	
    public function render()
    {
        return view('livewire.categoria-evento.create-component');
    }
	
	 public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Guardar datos validados
        $usuariosSave = CategoriaEvento::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 55, $usuariosSave->id));

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', '¡Categoría de evento registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la categoría de evento!', [
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
        return redirect()->route('categoria-evento.index');

    }
	
}
