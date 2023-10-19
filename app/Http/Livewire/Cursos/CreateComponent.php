<?php

namespace App\Http\Livewire\Cursos;

use App\Models\Cursos;
use App\Models\CursosCelebracion;
use App\Models\CursosDenominacion;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;
    public $denominacion_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $celebracion_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $fecha_inicio;
    public $fecha_fin;
    public $precio;
    public $duracion;
    public $descripcion;

    public $denominaciones;
    public $celebraciones;

    public function mount(){
        $this->denominaciones = CursosDenominacion::all(); // datos que se envian al select2
        $this->celebraciones = CursosCelebracion::all(); // datos que se envian al select2

    }

    public function render()
    {
        return view('livewire.cursos.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate([
            'nombre' => 'required',
            'denominacion_id' => 'required',
            'celebracion_id' => 'required',
            'precio' => 'required',
            'duracion' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'descripcion' => '',

        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'denominacion_id.required' => 'La denominación es obligatoria',
                'celebracion_id.required' => 'La celebracion es obligatoria.',
                'precio.required' => 'El precio es obligatorio',
                'duracion.required' => 'La duración es obligatoria',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_fin.required' => 'La fecha de fin es obligatoria',
            ]);

        // Guardar datos validados
        $cursosSave = Cursos::create($validatedData);

        // Alertas de guardado exitoso
        if ($cursosSave) {
            $this->alert('success', '¡Curso registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del curso!', [
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
        return redirect()->route('cursos.index');

    }
}
