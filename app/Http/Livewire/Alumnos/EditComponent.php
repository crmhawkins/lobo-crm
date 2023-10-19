<?php

namespace App\Http\Livewire\Alumnos;

use App\Models\Alumno;
use App\Models\Alumnos;
use App\Models\Empresa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;


class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $nombre;
    public $empresa_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $apellidos;
    public $dni;
    public $fecha_nac;
    public $direccion;
    public $localidad;
    public $provincia;
    public $cod_postal;
    public $pais;
    public $telefono;
    public $movil;
    public $email;

    public $empresas;

    public function mount()
    {
        $alumnos = Alumno::find($this->identificador);

        $this->empresas = Empresa::all(); // datos que se envian al select2


        $this->nombre = $alumnos->nombre;
        $this->empresa_id = $alumnos->empresa_id;
        $this->apellidos = $alumnos->apellidos;
        $this->dni = $alumnos->dni;
        $this->fecha_nac = $alumnos->fecha_nac;
        $this->direccion = $alumnos->direccion;
        $this->localidad = $alumnos->localidad;
        $this->provincia = $alumnos->provincia;
        $this->cod_postal = $alumnos->cod_postal;
        $this->pais = $alumnos->pais;
        $this->telefono = $alumnos->telefono;
        $this->movil = $alumnos->movil;
        $this->email = $alumnos->email;

    }

    public function render()
    {
        return view('livewire.alumnos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'nombre' => 'required',
            'empresa_id' => 'required',
            'apellidos' => 'required',
            'dni' => 'required',
            'fecha_nac' => 'required',
            'direccion' => 'required',
            'localidad' => 'required',
            'provincia' => 'required',
            'cod_postal' => 'required',
            'pais' => 'required',
            'telefono' => 'required',
            'movil' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellidos.required' => 'Los apellidos son obligatorios.',
                'dni.required' => 'El dni es obligatorio.',
                'fecha_nac.required' => 'La fecha de nacimiento es obligatoria.',
                'direccion.required' => 'La dirección es obligatoria.',
                'localidad.required' => 'La localidad es obligatoria.',
                'provincia.required' => 'La provincia es obligatoria.',
                'cod_postal.required' => 'El cod. postal es obligatorio.',
                'pais.required' => 'El cod. país es obligatorio.',
                'telefono.required' => 'El teléfono es obligatorio.',
                'movil.required' => 'El móvil es obligatorio.',
                'email.required' => 'El email es obligatorio.',
                'email.regex' => 'Introduce un email válido',
            ]);

        // Encuentra el alumno identificado
        $alumnos = Alumno::find($this->identificador);

        // Guardar datos validados
        $alumnosSave = $alumnos->update([
            'nombre' => $this->nombre,
            'empresa_id' => $this->empresa_id,
            'apellidos' => $this->apellidos,
            'dni' => $this->dni,
            'fecha_nac' => $this->fecha_nac,
            'direccion' => $this->direccion,
            'localidad' => $this->localidad,
            'provincia' => $this->provincia,
            'cod_postal' => $this->cod_postal,
            'pais' => $this->pais,
            'telefono' => $this->telefono,
            'movil' => $this->movil,
            'email' => $this->email,

        ]);

        if ($alumnosSave) {
            $this->alert('success', '¡Alumno actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del producto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Alumno actualizado correctamente.');

        $this->emit('productUpdated');
    }

      // Elimina el producto
      public function destroy(){
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el alumno? No hay vuelta atrás', [
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
        return redirect()->route('alumnos.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $alumnos = Alumno::find($this->identificador);
        $alumnos->delete();
        return redirect()->route('alumnos.index');

    }
}
