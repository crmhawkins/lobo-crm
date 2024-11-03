<?php

namespace App\Http\Livewire\Alertas;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Alertas;
use Livewire\WithFileUploads; // Importa el trait para manejar archivos
use Jantinnerezo\LivewireAlert\LivewireAlert;

class popup extends Component
{

    use WithFileUploads; // Añade el trait para manejar archivos
    use LivewireAlert;

    public $titulo;
    public $descripcion;
    public $imagen;
    public $usuariosSeleccionados = []; // Aquí se almacenan los usuarios seleccionados
    public $usuarios;
    public $alertas = [];

    public function mount()
    {
        if (Auth::user()->role == 'admin') {
            $this->alertas = Alertas::orderBy('id', 'desc')->where('popup', true)->get();
        } else {
            $this->alertas = Alertas::orderBy('id', 'desc')->where('popup', true)->where('user_id', Auth::user()->id)->get();
        }

        $this->usuarios = User::all();
    }

    public function enviarAlerta()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|image|max:1024', // Validación para la imagen, si es opcional
            'usuariosSeleccionados' => 'required|array|min:1', // Asegurarse de que se seleccionen usuarios
        ]);

        // Subir la imagen si existe
        $imagePath = null;
        if ($this->imagen) {
            $imagePath = $this->imagen->store('alertas', 'public'); // Guardar la imagen en el storage público
        }

        // Enviar la alerta a los usuarios seleccionados
        foreach ($this->usuariosSeleccionados as $usuarioId) {
            Alertas::create([
                'user_id' => $usuarioId,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'imagen' => $imagePath, // Guardar la ruta de la imagen si existe
                'leida' => false, // Marcar la alerta como no leída
                'popup' => true, // Marcar la alerta como una alerta emergente
            ]);
        }

        // Reiniciar los valores del formulario
        $this->reset(['titulo', 'descripcion', 'imagen', 'usuariosSeleccionados']);

        // Cerrar el modal y notificar que la alerta fue enviada
        $this->dispatchBrowserEvent('closeModal');
        session()->flash('success', '¡Alerta enviada con éxito!');
        $this->alert('success', '¡Alerta enviada correctamente!', [
            'position' => 'center',
            'timer' => 1000,
            'toast' => false,
            'showConfirmButton' => false,
            'timerProgressBar' => true,
        ]);
    }


    public function getNombreUsuario($id)
    {
        $user = User::find($id);
        return $user->name . ' ' . $user->apellidos;
    }


    public function render()
    {
        return view('livewire.alertas.popup');
    }
}
