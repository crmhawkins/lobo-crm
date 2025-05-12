<?php

namespace App\Http\Livewire\Eventos;

use App\Models\Evento;
use App\Models\Servicio;
use App\Models\Cliente;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $servicio;
    public $servicios;
    public $eventoServicios;

    public $clientes;

    public $eventoNombre;
    public $eventoProtagonista;
    public $eventoNiños;
    public $eventoContacto;
    public $eventoParentesco;
    public $eventoLugar;
    public $eventoMontaje;
    public $eventoLocalidad;
    public $eventoTelefono;
    public $diaEvento;
    public $diaFinal;


    public function mount()
    {
        $evento = Evento::find($this->identificador);

        $this->servicios = Servicio::all();
        $this->clientes = Cliente::all();

        $this->eventoServicios = $evento->servicios;

        $this->eventoNombre = $evento->eventoNombre;
        $this->eventoProtagonista = $evento->eventoProtagonista;
        $this->eventoNiños = $evento->eventoNiños;
        $this->eventoContacto = $evento->eventoContacto;
        $this->eventoParentesco = $evento->eventoParentesco;
        $this->eventoTelefono = $evento->eventoTelefono;
        $this->eventoLugar = $evento->eventoLugar;
        $this->eventoLocalidad = $evento->eventoLocalidad;
        $this->diaEvento = $evento->diaEvento;
        $this->diaFinal = $evento->diaFinal;
        $this->eventoMontaje = $evento->eventoMontaje;

    }

    public function addServicio(int $servicio)
    {
        $this->eventoServicios[$servicio] = $servicio;
    }

    public function nombreCliente(int $id){
        $nombreCompleto =  sprintf("%s %s", $this->clientes->find($id)->eventoNombre,  $this->clientes->find($id)->apellido);
        return $nombreCompleto;
    }


    public function render()
    {
        return view('livewire.eventos.edit-component');
    }

    public function crearServicio()
    {
        return Redirect::to(route("servicios.create"));
    }

    // Al hacer update en el formulario
    public function update()
    {
        if($this->diaEvento <= $this->diaFinal){
        // Validación de datos
        $this->validate([
            'eventoNombre' => 'required',
            'eventoProtagonista' => 'required',
            'eventoNiños' => 'required',
            'eventoContacto' => 'required',
            'eventoParentesco' => 'required',
            'eventoLugar'=> 'required',
            'eventoLocalidad' => 'required',
            'eventoMontaje' => '',
            'eventoTelefono' => 'required',
        ],
            // Mensajes de error
            [
                'eventoNombre.required' => 'El eventoNombre es obligatorio.',
                'eventoProtagonista.required' => 'Los eventoProtagonista son obligatorio.',
                'eventoNiños.required' => 'La cantidad de eventoNiños es obligatoria.',
                'eventoContacto.required' => 'El eventoNombre de usuario es obligatorio.',
                'eventoParentesco.required' => 'La contraseña es obligatoria.',
                'eventoLugar.required' => 'El eventoLugar es obligatorio.',
                'eventoLocalidad.required' => 'La eventoLocalidad es obligatoria.',
                'eventoTelefono.required' => 'El eventoTelefono es obligatorio.',

            ]);

        // Encuentra el identificador
        $evento = Evento::find($this->identificador);

        // Guardar datos validados
        $eventoSave = $evento->update([
            'eventoNombre' => $this->eventoNombre,
            'eventoProtagonista'=>$this->eventoProtagonista,
            'eventoNiños' => $this->eventoNiños,
            'eventoContacto' => $this->eventoContacto,
            'eventoParentesco' => $this->eventoParentesco,
            'eventoLugar' => $this->eventoLugar,
            'eventoLocalidad' => $this->eventoLocalidad,
            'eventoMontaje'=>$this->eventoMontaje,
            'eventoTelefono'=>$this->eventoTelefono,
            'diaEvento'=>$this->diaEvento,
            'diaFinal'=>$this->diaFinal,
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 12, $evento->id));

        if ($eventoSave) {
            $this->alert('success', 'Usuario actualizado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Evento actualizado correctamente.');

        $this->emit('eventUpdated');
    }else{
        $this->alert('error', 'la fecha final es mayor que la de inicio', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);
    }
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el usuario? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);

    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('eventos.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $evento = Evento::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 13, $evento->id));
        $evento->delete();
        return redirect()->route('eventos.index');

    }
}
