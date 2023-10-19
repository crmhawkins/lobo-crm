<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\DepartamentosUser;
use App\Models\User;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $name;
    public $surname;
    public $role = "Empleado"; // 0 por defecto por si no se selecciona ninguna
    public $username;
    public $despartamentos;
    public $password;
    public $email;
    public $user_department_id = 1;
    public $inactive;


    public function mount(){
        $this->despartamentos = DepartamentosUser::all();
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
            'user_department_id' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],

        ],
            // Mensajes de error
            [
                'name.required' => 'El nombre es obligatorio.',
                'surname.required' => 'El apellido es obligatorio.',
                'role.required' => 'El rol es obligatorio.',
                'username.required' => 'El nombre de usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'user_department_id.required' => 'El departamento es obligatorio.',
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
        return redirect()->route('usuarios.index');

    }
}
