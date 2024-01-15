<?php

namespace App\Http\Livewire\Proveedores;

use App\Models\Proveedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $proveedores;

    public $nombre;
    public $dni_cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;

    public $nota;

    public function mount()
    {
        $this->proveedores = Proveedores::all();
    }

    public function crearProveedores()
    {
        return Redirect::to(route("proveedores.create"));
    }



    public function render()
    {
        return view('livewire.proveedores.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {


        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'dni_cif' => 'required',
                'direccion' => 'required',
                'provincia' => 'required',
                'localidad' => 'required',
                'cod_postal' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'nota' => 'nullable',

            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'dni_cif.required' => 'El documento de identidad es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
                'provincia.required' => 'La provincia es obligatoria.',
                'localidad.required' => 'La localidad es obligatoria.',
                'cod_postal.required' => 'El código es obligatoria.',
                'telefono.required' => 'El telefono es obligatorio.',
                'email.required' => 'El email es obligatorio.',

            ]
        );

        // Guardar datos validados
        $proveedoresSave = Proveedores::create($validatedData);

        event(new \App\Events\LogEvent(Auth::user(), 8, $proveedoresSave->id));

        // Alertas de guardado exitoso
        if ($proveedoresSave) {
            $this->alert('success', '¡Proveedor registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del proveedor!', [
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
        return redirect()->route('proveedores.index');
    }
}
