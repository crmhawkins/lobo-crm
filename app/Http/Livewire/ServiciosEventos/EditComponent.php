<?php

namespace App\Http\Livewire\ServiciosEventos;

use App\Models\Servicio;
use App\Models\ServicioCategoria;
use App\Models\ServicioPack;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $servicioCategorias;
    public $servicioPacks;

    public $nombre;
    public $precioBase;
    public $id_pack; 
    public $id_categoria;
    public $minEmpleados;
    public $lugar;
    public $posibilidadMontaje;
    public $localidad;
    public $telefono;

    public function mount()
    {
        $servicio = Servicio::find($this->identificador);
        $this->servicioCategorias = ServicioCategoria::all();
        $this->servicioPacks = ServicioPack::all();

        $this->nombre = $servicio->nombre;
        $this->precioBase = $servicio->precioBase;
        $this->id_pack = $servicio->pack;
        $this->id_categoria = $servicio->categoria;
        $this->minEmpleados = $servicio->minEmpleados;

    }

    public function render()
    {
        return view('livewire.servicios.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'nombre' => 'required',
            'precioBase' => 'required',
            'id_pack' => 'required',
            'id_categoria' => 'required',
            'minEmpleados' => 'required',
        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'precioBase.required' => 'Los precioBase son obligatorio.',
                'id_pack.required' => 'La cantidad de pack es obligatoria.',
                'id_categoria.required' => 'El nombre de usuario es obligatorio.',
                'minEmpleados.required' => 'La contraseña es obligatoria.',
            ]);

        // Encuentra el identificador
        $servicio = Servicio::find($this->identificador);

        // Guardar datos validados
        $servicioSave = $servicio->update([
            'nombre' => $this->nombre,
            'precioBase'=>$this->precioBase,
            'id_pack' => $this->id_pack,
            'id_categoria' => $this->id_categoria,
            'minEmpleados' => $this->minEmpleados,
        ]);

        if ($servicioSave) {
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

        session()->flash('message', 'Servicio actualizado correctamente.');

        $this->emit('eventUpdated');
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
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('servicios.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $servicio = Servicio::find($this->identificador);
        $servicio->delete();
        return redirect()->route('servicios.index');

    }
}
