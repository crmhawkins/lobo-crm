<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\Rol;
use App\Models\User;
use App\Models\Almacen;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $name;
    public $surname;
    public $roles;
    public $role = 0; // 0 por defecto por si no se selecciona ninguna
    public $username;
    public $user_department_id = 1;
    public $despartamentos;
    public $password;
    public $email;
    public $inactive;
    public $almacen_id = 0;
    public $almacenes;
    public $telefono;



    public function mount(){
        $this->almacenes = Almacen::all();
        $this->roles = Rol::all();
    }

    public function render()
    {
        return view('livewire.usuarios.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        $this->password = Hash::make($this->password);
        // Validación de datos
        $validatedData = $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'role' => 'required',
            'username' => 'required',
            'password' => 'required',
            'almacen_id' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'telefono' => 'nullable',

        ],
            // Mensajes de error
            [
                'name.required' => 'El nombre es obligatorio.',
                'surname.required' => 'El apellido es obligatorio.',
                'role.required' => 'El rol es obligatorio.',
                'username.required' => 'El nombre de usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'email.required' => 'El email es obligatorio.',
                'email.regex' => 'Introduce un email válido',
            ]);

        // Guardar datos validados
        $validatedData['inactive'] = 0;
        $usuariosSave = User::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 26, $usuariosSave->id));

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', '¡Usuario registrado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
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
        return redirect()->route('usuarios.index');

    }
}
