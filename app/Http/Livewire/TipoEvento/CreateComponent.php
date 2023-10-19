<?php

namespace App\Http\Livewire\TipoEvento;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\TipoEvento;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{
	    use LivewireAlert;

    public $nombre;


    public function mount(){
    }
    public function render()
    {
        return view('livewire.tipo-evento.create-component');
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
        $usuariosSave = TipoEvento::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 52, $usuariosSave->id));

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', '¡Tipo de evento registrado correctamente!', [
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
        return redirect()->route('tipo-evento.index');

    }
}
