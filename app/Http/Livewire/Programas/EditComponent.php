<?php

namespace App\Http\Livewire\Programas;

use App\Models\Monitor;
use App\Models\Programa;
use Illuminate\Support\Facades\Redirect;
use App\Models\Servicio;
use App\Models\Evento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $servicios;
    public $monitores;
    public $eventos;

    public $dia;
    public $precioBase;
    public $id_servicio = 0; 
    public $id_evento = 0;
    public $id_monitor = 0;
    public $comienzoMontaje;
    public $comienzoEvento;
    public $horas;
    public $tiempoDesmontaje;


    public function mount()
    {
        $programa = Programa::find($this->identificador);
        $this->servicios = Servicio::all();
        $this->monitores = Monitor::all();
        $this->eventos = Evento::all();

        $this->dia = $programa->dia;
        $this->id_evento = $programa->id_evento;
        $this->id_servicio = $programa->id_servicio;
        $this->id_monitor = $programa->id_monitor;
        $this->comienzoMontaje = $programa->comienzoMontaje;
        $this->comienzoEvento = $programa->comienzoEvento;
        $this->horas = $programa->horas;
        $this->tiempoDesmontaje = $programa->tiempoDesmontaje;
    }



    public function nombreMonitor(int $id){
        $nombreCompleto =  sprintf("%s %s", $this->monitores->find($id)->nombre,  $this->monitores->find($id)->apellidos);
        return $nombreCompleto;
    }

    public function render()
    {
        return view('livewire.programas.edit-component');
    }

    public function crearEvento()
    {
        return Redirect::to(route("evento.create"));
    }

    public function crearServicio()
    {
        return Redirect::to(route("servicios.create"));
    }

    public function crearMonitor()
    {
        return Redirect::to(route("monitor.create"));
    }


    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'dia' => 'required',
            'id_evento' => 'required',
            'id_servicio' => 'required',
            'id_monitor' => 'required',
            'comienzoMontaje' => 'required',
            'comienzoEvento' => 'required',
            'tiempoDesmontaje' => 'required',
        ],
            // Mensajes de error
            [
                'dia.required' => 'El nombre es obligatorio.',
                'id_evento.required' => 'Los precioBase son obligatorio.',
                'id_servicio.required' => 'La cantidad de pack es obligatoria.',
                'id_monitor.required' => 'El nombre de usuario es obligatorio.',
                'comienzoMontaje.required' => 'La contraseña es obligatoria.',
                'comienzoEvento.required' => 'La contraseña es obligatoria.',
                'tiempoDesmontaje.required' => 'La contraseña es obligatoria.',
            ]);

        // Encuentra el identificador
        $programa = Programa::find($this->identificador);

        // Guardar datos validados
        $programaSave = $programa->update([
            'dia' => $this->dia,
            'id_evento'=>$this->id_evento,
            'id_servicio' => $this->id_servicio,
            'id_monitor' => $this->id_monitor,
            'comienzoMontaje' => $this->comienzoMontaje,
            'comienzoEvento' => $this->comienzoEvento,
            'horas' => $this->horas,
            'tiempoDesmontaje' => $this->tiempoDesmontaje,

        ]);

        if ($programaSave) {
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
        return redirect()->route('programas.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $programa = Programa::find($this->identificador);
        $programa->delete();
        return redirect()->route('programas.index');

    }
}
