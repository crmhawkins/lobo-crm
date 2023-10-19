<?php

namespace App\Http\Livewire\Empresas;

use App\Models\Empresa;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;
    public $telefono;
    public $direccion;
    public $cif;
    public $email;
    public $cod_postal;
    public $localidad;
    public $pais;




    public function mount(){
    }

    public function render()
    {
        return view('livewire.empresas.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',
            'telefono' => 'required|numeric',
            'direccion' => 'required',
            'cif' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'cod_postal' => 'required',
            'localidad' => 'required',
            'pais' => 'required',

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'telefono.required' => 'El teléfono es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
                'cif.required' => 'El CIF es obligatorio.',
                'email.required' => 'El email es obligatorio.',
                'email.regex' => 'Introduce un email válido',
                'cod_postal.required' => 'El código postal es obligatorio.',
                'localidad.required' => 'La localidad es obligatoria.',
                'pais.required' => 'El país es obligatorio.',
            ]);

        // Guardar datos validados
        $empresasSave = Empresa::create($validatedData);

        // Alertas de guardado exitoso
        if ($empresasSave) {
            $this->alert('success', '¡Empresa registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la empresa!', [
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
        return redirect()->route('empresas.index');

    }
}
