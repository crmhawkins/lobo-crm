<?php

namespace App\Http\Livewire\ResumenSemanas;

use App\Models\Monitor;
use App\Models\Cliente;
use App\Models\Programa;
use Illuminate\Support\Facades\Redirect;
use App\Models\Servicio;
use App\Models\Evento;
use App\Models\ResumenDia;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $dia;
    public $programas;
    public $clientes;
    public $eventos;
    public $monitores;

    public $selectedEventos=[];

    public $programasEvento;
    public $servicioPrograma;


    public function mount()
    {
        $
        $this->dia = $resumen->dia;

        $this->eventos = Evento::where("diaInicio" <= $this->dia && "diaFinal" >= $this->dia);
        $this->programas = Programa::where("dia" == $this->dia);


       $eventosId = [];
       foreach($this->eventos as $evento){
            array_push($eventosId, $evento->id);
       }

        //$this->id_programa = $resumen->id_programa;
        // $this->id_cliente = $resumen->id_cliente;

        
        $this->clientes = Cliente::whereIn("id", $eventosId);

    }



    public function render()
    {
        return view('livewire.resumen-dia.edit-component');
    }

    public function crearEvento()
    {
        return Redirect::to(route("resumen-dia.create"));
    }

    public function crearServicio()
    {
        return Redirect::to(route("resumen-dia.create"));
    }

    public function crearMonitor()
    {
        return Redirect::to(route("resumen-dia.create"));
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
