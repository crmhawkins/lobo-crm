<?php

namespace App\Http\Livewire\Programas;

use Illuminate\Support\Facades\Redirect;
use App\Models\Servicio;
use App\Models\Monitor;
use App\Models\Evento;
use App\Models\Programa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $programa;
    public $servicios;
    public $monitores;
    public $eventos;

    public $dia;
    public $precioBase;
    public $id_servicio=0; 
    public $id_evento=0;
    public $id_monitor;
    public $comienzoMontaje;
    public $comienzoEvento;
    public $horas;
    public $tiempoDesmontaje;
    public $precioMonitor;


    public function mount(){
        $this->programa = Programa::all();
        $this->servicios = Servicio::all();
        $this->monitores = Monitor::all();
        $this->eventos = Evento::all();
    }

    public function nombreMonitor(int $id){
        $nombreCompleto =  sprintf("%s %s", $this->monitores->find($id)->nombre,  $this->monitores->find($id)->apellidos);
        return $nombreCompleto;
    }

    public function render()
    {
        return view('livewire.programas.create-component');
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

    // Al hacer submit en el formulario
    public function submit()
    {
        
        // Validación de datos
        $validatedData = $this->validate([
            'dia' => 'required',
            'id_evento' => 'required',
            'id_servicio' => 'required',
            'id_monitor' => 'required',
            'precioMonitor'=> 'required',
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
                'precioMonitor.required' => 'La contraseña es obligatoria.',
                'comienzoEvento.required' => 'La contraseña es obligatoria.',
                'tiempoDesmontaje.required' => 'La contraseña es obligatoria.',
            ]);
        // Guardar datos validados
        $programaSave = Programa::create(array_merge($validatedData, ["horas" => $this->horas]));

        // Alertas de guardado exitoso
        if ($programaSave) {
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
        return redirect()->route('programas.index');

    }
}
