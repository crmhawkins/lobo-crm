<?php

namespace App\Http\Livewire\Monitores;

use App\Models\Cliente;
use App\Models\Evento;
use App\Models\Monitor;
use App\Models\Presupuesto;
use App\Models\Programa;
use App\Models\Servicio;
use App\Models\ServicioEvento;
use App\Models\ServicioPresupuesto;
use App\Models\TipoEvento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $nombre;
    public $apellidos;
    public $contacto;
    public $parentesco;
    public $lugar;
    public $fechaNa;
    public $localidad;
    public $telefono;
    public $alias;
    public $dni;
    public $num_ss;
    public $nivel_estudios;
    public $domicilio;
    public $provincia;
    public $codigo_postal;
    public $nombre_padre;
    public $nombre_madre;
    public $email;


    //Historial
    public $programas;
    public $eventos;
    public $servicios;
    public $presupuestos;
    public $categorias;
    public $clientes;
    public $vecesContratado;

    //debug
    public $eventos_id;

    public function mount()
    {
        $monitor = Monitor::find($this->identificador);

        $this->presupuestos = Presupuesto::all();
        $this->nombre = $monitor->nombre;
        $this->apellidos = $monitor->apellidos;
        $this->telefono = $monitor->telefono;
        $this->alias = $monitor->alias;
        $this->dni = $monitor->dni;
        $this->num_ss = $monitor->num_ss;
        $this->nivel_estudios = $monitor->nivel_estudios;
        $this->domicilio = $monitor->domicilio;
        $this->provincia = $monitor->provincia;
        $this->codigo_postal = $monitor->codigo_postal;
        $this->nombre_padre = $monitor->nombre_padre;
        $this->nombre_madre = $monitor->nombre_madre;
        $this->email = $monitor->email;
        $this->fechaNa = $monitor->fechaNa;
        $this->lugar = $monitor->lugar;
        $this->localidad = $monitor->localidad;
        $this->eventos = ServicioPresupuesto::whereJsonContains('id_monitores', $this->identificador)->get()->groupBy('servicio_id')->toArray();
        $this->clientes = Cliente::all();
        $this->eventos_id = Evento::all();
        $this->categorias = TipoEvento::all();
        $this->programas = Programa::where('id_monitor', $this->identificador)->get();
        $servicios_id = $this->programas->pluck("id_servicio")->toArray();
        $this->servicios =  Servicio::all();

        $this->vecesContratado = count($this->programas);
    }

    public function getEventoFromIdServicioEvento($id)
    {
        $servicioEvento = ServicioEvento::where("id", $id)->first();

        $evento = Evento::where("id", $servicioEvento->id_evento)->first();

        return $evento->eventoNombre;
    }
    public function getServicioFromIdServicioEvento($id)
    {
        $servicioEvento = ServicioEvento::where("id", $id)->first();

        $servicio = Servicio::where("id", $servicioEvento->id_servicio)->first();

        return $servicio->nombre;
    }

    public function render()
    {
        return view('livewire.monitores.edit-component');
    }

    // public function crearServicio()
    // {
    //     return Redirect::to(route("servicios.create"));
    // }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate(
            [
                'nombre' => 'required',
                'apellidos' => 'required',
                'localidad' => 'required',
                'telefono' => 'required',
                'alias' => 'nullable',
                'dni' => 'nullable',
                'num_ss' => 'nullable',
                'nivel_estudios' => 'nullable',
                'domicilio' => 'nullable',
                'provincia' => 'nullable',
                'codigo_postal' => 'nullable',
                'nombre_padre' => 'nullable',
                'nombre_madre' => 'nullable',
                'email' => 'nullable',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellidos.required' => 'Los protagonistas son obligatorio.',
                'localidad.required' => 'La localidad es obligatoria.',
                'telefono.required' => 'El telefono es obligatorio.',

            ]
        );

        // Encuentra el identificador
        $monitor = Monitor::find($this->identificador);

        // Guardar datos validados
        $monitorSave = $monitor->update([
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'localidad' => $this->localidad,
            'telefono' => $this->telefono,
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 21, $monitor->id));

        if ($monitorSave) {
            $this->alert('success', 'Monitor actualizado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del monitor!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Monitor actualizado correctamente.');

        $this->emit('eventUpdated');
    }

    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar el monitor? No hay vuelta atrás', [
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
            'update',
            'destroy'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('monitor.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $monitor = Monitor::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 22, $monitor->id));
        $monitor->delete();
        return redirect()->route('monitor.index');
    }

    public function getCliente($id)
    {
        return $this->clientes->firstWhere('id', $this->presupuestos->firstWhere('id', $id)->id_cliente)->nombre . " " . $this->clientes->firstWhere('id', $this->presupuestos->firstWhere('id', $id)->id_cliente)->apellido;
    }
}
