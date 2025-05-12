<?php

namespace App\Http\Livewire\Monitores;

use App\Models\Monitor;
//use App\Models\Servicio;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $identificador;

    public $nombre;
    public $apellidos;
    public $contacto;
    public $parentesco;
    public $fechaNa;
    public $lugar;
    public $localidad;
    public $telefono;
    public $alias;
    public $dni;
    public $num_ss;
    public $nivel_estudios;
    public $domicilio;
    public $provincia;
    public $codigo_postal;
    public $nombre_padre;
    public $nombre_madre;
    public $email;


    public function mount()
    {
    }



    public function render()
    {
        return view('livewire.monitores.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'apellidos' => 'required',
                'localidad' => 'required',
                'telefono' => 'required',
                'fechaNa' => 'required',
                'alias'  => 'nullable',
                'dni' => 'nullable',
                'num_ss' => 'nullable',
                'nivel_estudios' => 'nullable',
                'domicilio' => 'nullable',
                'provincia' => 'nullable',
                'codigo_postal' => 'nullable',
                'nombre_padre' => 'nullable',
                'nombre_madre' => 'nullable',
                'email' => 'nullable',

            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellidos.required' => 'Los protagonistas son obligatorio.',
                'localidad.required' => 'La localidad es obligatoria.',
                'telefono.required' => 'El telefono es obligatorio.',
                'fechaNa.required' => 'El telefono es obligatorio.',

            ]
        );

        // Guardar datos validados
        $monitorSave = Monitor::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 20, $monitorSave->id));

        // Alertas de guardado exitoso
        if ($monitorSave) {
            $this->alert('success', '¡Monitor registrado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del monitor!', [
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
        return redirect()->route('monitor.index');
    }
}
