<?php

namespace App\Http\Livewire\Eventos;

use App\Models\Evento;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Programa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Integer;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $id_evento;

    public $diaEvento;
    public $servicio;
    public $servicios;
    public $eventoServicios;
    public $programas;
    public $listaProgramas;
    public $clientes;


    public $eventoNombre;
    public $eventoProtagonista;
    public $eventoNiños;
    public $eventoAdultos;
    public $eventoContacto;
    public $eventoParentesco;
    public $eventoLugar;
    public $eventoMontaje;
    public $eventoLocalidad;
    public $eventoTelefono;
    public $diaFinal;

    public $nDias;

    public $addPrograma = false;
    public $addCliente = false;
    public $addServicio = false;

    protected $listeners = ['actualizarFecha' => 'actualizarFecha'];

    public function mount()
    {
        $this->servicios = Servicio::all();
        $this->clientes = Cliente::all();
        $this->id_evento = Evento::max("id")+ 1;
        $this->eventoServicios = [];
    }

    public function nombreCliente(int $id){
        $nombreCompleto =  sprintf("%s %s", $this->clientes->find($id)->eventoNombre,  $this->clientes->find($id)->apellido);
        return $nombreCompleto;
    }

    public function crearServicio()
    {
        return Redirect::to(route("servicios.create"));
    }

    public function crearCliente()
    {
        $this->addCliente = true;
    }

    public function clienteSelected(){
        $this->addCliente = false;
        if($this->eventoTelefono === null || $this->eventoTelefono ===""){
            $this->eventoTelefono = Cliente::find($this->eventoContacto)->first()->tlf1;
        }
    }
    public function addServicio($servicio)
    {
        if($servicio === 0){
            $this->addServicio = true;
        }else{
            // $this->eventoServicios[$servicio] = $this->servicios->find($servicio);
        }
        
    }

    public function diasServicio(){
        if($this->diaEvento === $this->diaFinal){
            $this->diaEvento = $this->diaEvento;
            $this->nDias = 1;
        }else{
            $this->nDias = date_diff($this->diaEvento, $this->diaFinal)->format("%R%a");
        }
    }

    public function addPrograma($datosPrograma){
        if(count($datosPrograma) === 11){
            $this->listaProgramas[$datosPrograma[0]] = $datosPrograma;
        }else{
            $this->addPrograma = true;
        }
    }
    

    public function getProgramaByServicioAndDia($id_servicio, $diaEvento){
       return $this->programas->where("id_servicio", "==", $id_servicio, "&&", "diaEvento", "==", $diaEvento);
    }

    public function getAllDayPrograms($diaEvento, $id_evento){
        return $this->programas->where("id_evento", "==", $id_evento, "&&", "diaEvento", "==", $diaEvento);
    }

    
    

    public function render()
    {
        return view('livewire.eventos.create-component');
    }

    public function actualizarFecha()
    {
        $this->diaFinal = $this->diaEvento;
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        // if($this->diaFinal === null  || $this->diaFinal === ""){
        //     $this->diaFinal = $this->diaEvento;
        // }
        // if($this->diaEvento <= $this->diaFinal){
        // Validación de datos
        $validatedData = $this->validate(
            [
                'eventoNombre' => 'required',
                'eventoProtagonista' => 'required',
                'eventoNiños' => 'required',
                'eventoContacto' => 'required',
                'eventoParentesco' => 'required',
                'eventoLugar' => 'required',
                'eventoLocalidad' => 'required',
                'eventoMontaje' => '',
                'eventoTelefono' => 'required',
                'diaEvento' => 'required',
                'diaFinal' => 'required',
                
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
                'diaEvento.required' => 'El eventoTelefono es obligatorio.',
                'diaFinal.required' => 'El eventoTelefono es obligatorio.',
            ]
        );

        // Guardar datos validados
        $usuariosSave = Evento::create($validatedData);

        event(new \App\Events\LogEvent(Auth::user(), 11, $usuariosSave->id));

        // dd($usuariosSave);
        // Alertas de guardado exitoso
        if ($usuariosSave) {

            $this->alert('success', '¡Usuario registrado correctamente!', [
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
    // }else{
    //     $this->alert('error', 'la fecha final es mayor que la de inicio', [
    //         'position' => 'center',
    //         'timer' => 3000,
    //         'toast' => false,
    //     ]);
    // }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        // return redirect()->route('eventos.index');
        return redirect()->route('eventos.index');
        
    }
}
