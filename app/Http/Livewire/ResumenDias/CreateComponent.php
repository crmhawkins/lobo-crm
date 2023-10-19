<?php

namespace App\Http\Livewire\ResumenDias;

use Illuminate\Support\Facades\Redirect;
use App\Models\ResumenDia;

use App\Models\Cliente;
use App\Models\Monitor;
use App\Models\Evento;
use App\Models\Programa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;


    public $clientes;
    public $programas;
    public $servicio;
    public $monitore;
    public $eventos;
    
    

    public $dia;
    public $id_programa = 0; 
    public $id_cliente = 0;
    
    public $id_evento;
    public $nombre_evento;


    public function mount()
    {

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
