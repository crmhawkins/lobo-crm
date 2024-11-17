<?php

namespace App\Http\Livewire\Alertas;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Alertas;
use Livewire\WithFileUploads; // Importa el trait para manejar archivos
use Jantinnerezo\LivewireAlert\LivewireAlert;

use Illuminate\Support\Facades\Mail;
use App\Mail\AlertasMail;
use Livewire\WithPagination; // Añadir este import



class popup extends Component
{

    use WithFileUploads; // Añade el trait para manejar archivos
    use LivewireAlert;
    use WithPagination; // Añadir este trait

    public $titulo;
    public $descripcion;
    public $imagen;
    public $usuariosSeleccionados = []; // Aquí se almacenan los usuarios seleccionados
    public $usuarios;

    protected $paginationTheme = 'bootstrap'; // Añadir esta propiedad si usas Bootstrap

    public function mount()
    {
        $this->usuarios = User::all();
    }

    public function getAlertas()
    {
        if (Auth::user()->role == 1) {
            return Alertas::orderBy('id', 'desc')
                ->where('popup', true)
                ->paginate(10);
        } else {
            return Alertas::orderBy('id', 'desc')
                ->where('popup', true)
                ->where('user_id', Auth::user()->id)
                ->paginate(10);
        }
    }

    public function enviarAlerta()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|image|max:1024',
            'usuariosSeleccionados' => 'required|array|min:1',
        ]);

        try {
            // Subir la imagen si existe
            $imagePath = $this->imagen ? $this->imagen->store('alertas', 'public') : null;

            // Obtener todos los usuarios seleccionados de una vez
            $usuarios = User::whereIn('id', $this->usuariosSeleccionados)->get();

            // Enviar correos y crear alertas en una sola iteración
            foreach ($usuarios as $user) {


                Alertas::create([
                    'user_id' => $user->id,
                    'titulo' => $this->titulo,
                    'descripcion' => $this->descripcion,
                    'imagen' => $imagePath,
                    'leida' => false,
                    'popup' => true,
                ]);

                Mail::to($user->email)
                    ->cc('Alejandro.martin@serlobo.com')
                    ->send(new AlertasMail(
                        $user,
                        $this->titulo,
                        $this->descripcion
                    ));
            }

            $this->reset(['titulo', 'descripcion', 'imagen', 'usuariosSeleccionados']);
            $this->dispatchBrowserEvent('closeModal');

            $this->alert('success', '¡Alerta enviada correctamente!', [
                'position' => 'center',
                'timer' => 1000,
                'toast' => false,
                'showConfirmButton' => false,
                'timerProgressBar' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error al enviar la alerta: ' . $e->getMessage());
        }
    }

    public function getNombreUsuario($id)
    {
        return User::find($id)?->name . ' ' . User::find($id)?->apellidos ?? 'Usuario no encontrado';
    }

    public function render()
    {
        return view('livewire.alertas.popup', [
            'alertas' => $this->getAlertas()
        ]);
    }
}
