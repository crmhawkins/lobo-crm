<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\User;
use App\Models\DepartamentosUser;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $name;
    public $surname;
    public $role;
    public $username;
    public $password;
    public $email;
    public $inactive;

    public function mount()
    {
        $usuarios = User::find($this->identificador);
        $this->despartamentos = DepartamentosUser::all();

        $this->name = $usuarios->name;
        $this->surname = $usuarios->surname;
        $this->role = $usuarios->role;
        $this->username = $usuarios->username;
        $this->password = $usuarios->password;
        $this->email = $usuarios->email;
        $this->inactive = $usuarios->inactive;

    }

    public function render()
    {
        return view('livewire.usuarios.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'role' => 'required',
            'username' => 'required',
            'password' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
        ],
            // Mensajes de error
            [
                'name.required' => 'El nombre es obligatorio.',
                'surname.required' => 'El apellido es obligatorio.',
                'role.required' => 'El rol es obligatorio.',
                'username.required' => 'El nombre de usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'email.required' => 'El código postal es obligatorio.',
                'email.regex' => 'Introduce un email válido',

            ]);

        // Encuentra el identificador
        $usuarios = User::find($this->identificador);

        // Guardar datos validados
        $usuariosSave = $usuarios->update([
            'name' => $this->name,
            'surname' => $this->surname,
            'role' => $this->role,
            'username' => $this->username,
            'password' => $this->password,
            'email' => $this->email,
            'incative'=>$this->inactive,
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 27, $usuarios->id));

        if ($usuariosSave) {
            $this->alert('success', 'Usuario actualizado correctamente!', [
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

        session()->flash('message', 'Usuario actualizado correctamente.');

        $this->emit('userUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el usuario? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);

    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'destroy',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('usuarios.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $usuarios = User::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 28, $usuarios->id));
        $usuarios->delete();
        return redirect()->route('usuarios.index');

    }
}
