<?php

namespace App\Http\Livewire\ServiciosEventos;

use App\Models\Servicio;
use App\Models\ServicioCategoria;
use App\Models\ServicioPack;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;
    public $precioBase;
    public $id_pack; 
    public $id_categoria;
    public $minEmpleados;
    public $servicioCategorias;
    public $servicioPacks;


    public function mount(){
        $this->servicioCategorias = ServicioCategoria::all();
        $this->servicioPacks = ServicioPack::all();
    }

    public function render()
    {
        return view('livewire.servicios.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',
            'precioBase' => 'required',
            'id_pack' => 'required',
            'id_categoria' => 'required',
            'minEmpleados' => 'required',

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'precioBase.required' => 'El precio base son obligatorio.',
                'id_pack.required' => 'El campo pack es obligatoria.',
                'id_categoria.required' => 'El nombre de usuario es obligatorio.',
                'minEmpleado.required' => 'El nombre de usuario es obligatorio.',
            ]);

        // Guardar datos validados
        $usuariosSave = Servicio::create($validatedData);

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', 'Servicio registrado correctamente!', [
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
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('servicios.index');

    }
}
