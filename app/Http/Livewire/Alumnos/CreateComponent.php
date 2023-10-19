<?php

namespace App\Http\Livewire\Alumnos;

use App\Models\Alumno;
use App\Models\Empresa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;
    public $empresa_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $apellidos;
    public $dni;
    public $fecha_nac;
    public $direccion;
    public $localidad;
    public $provincia;
    public $cod_postal;
    public $pais;
    public $telefono;
    public $movil;
    public $email;

    public $empresas;



    public function mount(){

        $this->empresas = Empresa::all(); // datos que se envian al select2

    }

    public function render()
    {
        return view('livewire.alumnos.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',
            'empresa_id' => 'required',
            'apellidos' => 'required',
            'dni' => 'required',
            'fecha_nac' => 'required',
            'direccion' => 'required',
            'localidad' => 'required',
            'provincia' => 'required',
            'cod_postal' => 'required',
            'pais' => 'required',
            'telefono' => 'required',
            'movil' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellidos.required' => 'Los apellidos son obligatorios.',
                'dni.required' => 'El dni es obligatorio.',
                'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
                'direccion.required' => 'La dirección es obligatoria.',
                'localidad.required' => 'La localidad es obligatoria.',
                'provincia.required' => 'La provincia es obligatoria.',
                'cod_postal.required' => 'El cod. postal es obligatorio.',
                'pais.required' => 'El cod. país es obligatorio.',
                'telefono.required' => 'El teléfono es obligatorio.',
                'movil.required' => 'El móvil es obligatorio.',
                'email.required' => 'El email es obligatorio.',
                'email.regex' => 'Introduce un email válido',
            ]);

        // Guardar datos validados
        $alumnosSave = Alumno::create($validatedData);

        // Alertas de guardado exitoso
        if ($alumnosSave) {
            $this->alert('success', '¡Alumno registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del alumno!', [
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
        return redirect()->route('alumnos.index');

    }
}
