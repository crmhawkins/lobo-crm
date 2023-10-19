<?php

namespace App\Http\Livewire\Cursos;

use App\Models\Cursos;
use App\Models\CursosCelebracion;
use App\Models\CursosDenominacion;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

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

    public function mount()
    {
        $cursos = Cursos::find($this->identificador);
        $this->denominaciones = CursosDenominacion::all(); // datos que se envian al select2
        $this->celebraciones = CursosCelebracion::all(); // datos que se envian al select2

        $this->nombre = $cursos->nombre;
        $this->denominacion_id = $cursos->denominacion_id;
        $this->celebracion_id = $cursos->celebracion_id;
        $this->fecha_inicio = $cursos->fecha_inicio;
        $this->fecha_fin = $cursos->fecha_fin;
        $this->precio = $cursos->precio;
        $this->duracion = $cursos->duracion;
        $this->descripcion = $cursos->descripcion;


    }

    public function render()
    {
        return view('livewire.cursos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
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
                'precio.required' => 'El precio es obligatorio.',
                'duracion.required' => 'La duración es obligatoria.',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_fin.required' => 'La fecha de fin es obligatoria',
            ]);

        // Encuentra el identificador
        $cursos = Cursos::find($this->identificador);

        // Guardar datos validados
        $cursosSave = $cursos->update([
            'nombre' => $this->nombre,
            'denominacion_id' => $this->denominacion_id,
            'celebracion_id' => $this->celebracion_id,
            'precio' => $this->precio,
            'duracion' => $this->duracion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'descripcion' => $this->descripcion,

        ]);

        if ($cursosSave) {
            $this->alert('success', 'Curso actualizado correctamente!', [
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

        session()->flash('message', 'Curso actualizado correctamente.');

        $this->emit('productUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el curso? No hay vuelta atrás', [
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
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('cursos.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $cursos = Cursos::find($this->identificador);
        $cursos->delete();
        return redirect()->route('cursos.index');

    }
}
