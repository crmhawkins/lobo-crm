<?php

namespace App\Http\Livewire\Presupuestos;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\TipoEvento;
use App\Models\CategoriaEvento;
use App\Models\Evento;
use App\Models\ServicioEvento;
use App\Models\Monitor;
use App\Models\Presupuesto;
use App\Models\Programa;
use App\Models\Servicio;
use App\Models\ServicioPack;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


use function PHPUnit\Framework\isEmpty;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $diaMostrar;
    public $contrato_id;
    public $ruta;
    public $cuentaTransferencia;
    public $metodoPago;
    public $authMenores;
    public $authImagen;
    public $identificador;
    public $currentStep = 1; // Pasos para el formulario, 1 es el comienzo y 3 el final
    public $nPresupuesto; // Numero de presupusto
    public $fechaEmision; // Fecha del presupuesto
    public $fechaVencimiento; // Fecha del presupuesto
    public $clienteSeleccionado; // Cliente seleccionado

    public $preciosBasePack = [];
    public $tipos_evento;
    public $categorias_evento;


    public $id_evento = 0; // 0 por defecto por si no se selecciona ninguna
    public $id_cliente = 0; // 0 por defecto por si no se selecciona ninguna
    public $observaciones = "";

    //cliente
    public $editandoCliente = 0;
    public $estado = "Pendiente";

    public $trato = null;
    public $nombre;
    public $apellido;
    public $tipoCalle;
    public $calle;
    public $numero;
    public $direccionAdicional1 = "";
    public $direccionAdicional2 = "";
    public $direccionAdicional3 = "";
    public $codigoPostal;
    public $ciudad;
    public $nif;
    public $tlf1;
    public $tlf2 = 0;
    public $tlf3 = 0;
    public $email1;
    public $email2 = "No";
    public $email3 = "No";
    public $confPostal = false;
    public $confEmail = false;
    public $confSms = false;

    public $clienteIsSave;

    public $clientes;

    public $cliente;

    public $clienteExistente = false;

    //Evento
    public $evento;
    public $eventoNombre;
    public $eventoProtagonista;
    public $eventoNiños;
    public $eventoAdulto;
    public $eventoContacto;
    public $eventoParentesco;
    public $eventoTelefono;
    public $eventoLugar;
    public $eventoLocalidad;
    public $eventoMontaje;
    public $diaFinal;
    public $diaEvento;
    public $dias = [];

    public $eventoIsSave = false;
    public $editarEvento = 0;

    //monitores
    public $monitores;
    public $id_monitor;
    public $desplazamiento;

    //Selector

    public $tipo_seleccionado;
    public $id_pack;

    public $preciosMonitores = [];
    public $numero_monitores = 0;

    public $precioFinalPack = 0;
    public $precioFinalServicio = 0;


    public $tiempo = 0;
    public $hora_inicio = 0;
    public $hora_finalizacion = 0;


    // Servicios
    public $id_servicio;
    public $comienzoMontaje;
    public $horaInicio;
    public $tiempodDesmontaje;
    public $precioMonitor;
    public $costoDesplazamiento;
    public $dia;
    public $minMonitores;
    public $numMonitores;
    public $importeBase;
    public $descuentoServicio;
    public $importeServicioDesc;

    public $serviciosListDia = [];
    public $servicioEventoList = [];

    public $servicioIsSave;


    public $servicios;

    public $precioServicio;
    public $servicio;

    public $packs;
    public $pack_seleccionado = 0;

    public $servicio_seleccionado = 0;

    public $listaPacks = [];

    public $eventoServicios;
    public $programas;
    public $listaServicios = [];
    public $tiemposPack = [];

    public $tiempoMontaje;
    public $tiempoDesmontaje;
    public $horaMontaje;

    public $tiemposMontajePack = [];
    public $tiemposDesmontajePack = [];

    public $horasInicioPack = [];
    public $horasFinalizacionPack = [];
    public $horasMontajePack = [];
    public $idMonitoresPack = [];
    public $sueldoMonitoresPack = [];
    public $gastosGasoilPack = [];
    public $pagosPendientesPack = [];

    public $idMonitores = [];
    public $sueldoMonitores = [];
    public $gastosGasoil = [];
    public $pagosPendientes = [];


    //pricing
    public $precioBase;
    public $descuento = 0;
    public $precioFinal = 0;
    public $entrega;
    public $adelanto = 0;


    //Descuentos
    public $descTotal = 0;
    public $entregaDiscount;
    public $totalDiscount;


    //triggers
    public $addDiscount = false;
    public $addPrograma = false;
    public $addCliente = false;
    public $addServicio = false;
    public $addObservaciones = false;
    public $categoria_evento_id = 0;


    //Debug
    public $baseMonitor;
    public $servicioEvento;
    public $key = 0;
    public $currentProgram;
    public $validatePrograms;
    public $type;
    public $gestor_id;
    public $gasoilDistancia;
    public $gasoilPrecio;
    public $clienteNuevo = false;
    public $mensajeCliente = false;
    public $nombreGestor;
    protected $listeners = ['rerender' => '$refresh'];

    public function mount()
    {

        $this->clientes = Cliente::all(); // datos que se envian al select2
        $this->monitores = Monitor::all();
        $this->servicios = Servicio::all();
        $this->tipos_evento = TipoEvento::all();
        $this->categorias_evento = CategoriaEvento::all();
        $this->gestor_id = Auth::id();
        $this->categoria_evento_id = 4;
        $year = Carbon::now()->format('Y');
        $numero = Presupuesto::whereBetween('fechaEmision', [$year . '-01-01', $year . '-12-31'])->count();
        $this->nPresupuesto = str_pad($numero + 1, 4, "0", STR_PAD_LEFT) . '/' . $year;
        $this->packs = ServicioPack::all();
        $this->gasoilPrecio = Settings::where('id', 1)->first()->precio_gasoil_km;
        $this->fechaEmision = date("Y-m-d", time());
        $this->nombreGestor = Auth::user()->name . " " . Auth::user()->surname;
        $this->servicioEventoList = array();
        if (session('datos2')) {
            $this->nPresupuesto = session('datos')['nPresupuesto'];
            $this->gestor_id = session('datos')['gestor_id'];
            $this->fechaEmision = session('datos')['fechaEmision'];
            $this->categoria_evento_id = session('datos')['categoria_evento_id'];
            $this->estado = session('datos')['estado'];
            $this->fechaVencimiento = session('datos')['fechaVencimiento'];
            $this->id_cliente = session('datos2');
        }
        if (session('datos3')) {
            $this->nPresupuesto = session('datos')['nPresupuesto'];
            $this->gestor_id = session('datos')['gestor_id'];
            $this->fechaEmision = session('datos')['fechaEmision'];
            $this->categoria_evento_id = session('datos')['categoria_evento_id'];
            $this->estado = session('datos')['estado'];
            $this->fechaVencimiento = session('datos')['fechaVencimiento'];
            $this->eventoNombre = session('datos3');
        }
    }

    public function cambiarPresupuesto()
    {
        $year = Carbon::parse($this->fechaEmision)->format('Y');
        $numero = Presupuesto::whereBetween('fechaEmision', [$year . '-01-01', $year . '-12-31'])->count();
        $this->nPresupuesto = str_pad($numero + 1, 4, "0", STR_PAD_LEFT) . '/' . $year;
    }

    public function render()
    {
        $this->dispatchBrowserEvent('initializeMapKit');
        $this->clienteSeleccionado = Cliente::find($this->id_cliente);
        return view('livewire.presupuestos.create-component');
    }

    public function setupEvento($id)
    {
        $this->evento = Evento::find($id);

        $this->eventoNombre = $this->evento->eventoNombre;
        $this->eventoProtagonista = $this->evento->eventoProtagonista;
        $this->eventoNiños = $this->evento->eventoNiños;
        $this->eventoContacto = $this->evento->eventoContacto;
        $this->eventoParentesco = $this->evento->eventoParentesco;
        $this->eventoTelefono = $this->evento->eventoTelefono;
        $this->eventoLugar = $this->evento->eventoLugar;
        $this->eventoLocalidad = $this->evento->eventoLocalidad;
        $this->eventoMontaje = $this->evento->eventoMontaje;
        $this->diaFinal = $this->evento->diaFinal;
        $this->diaEvento = $this->evento->diaEvento;
        $this->getDias();
    }

    public function prueba()
    {
        $this->id_cliente = 0;
    }
    public function clienteNuevoFunction()
    {
        $this->clienteNuevo = true;
        $this->clienteExistente = false;
        $this->mensajeCliente = true;
    }
    public function clienteExisteFunction()
    {
        $this->clienteNuevo = false;
        $this->clienteExistente = true;
        $this->mensajeCliente = true;
    }
    public function setupCliente($id)
    {
        // $this->cliente = $this->clientes->find($id);
        // if ($this->cliente) {
        //     $this->trato = $this->cliente->trato;
        //     $this->nombre = $this->cliente->nombre;
        //     $this->apellido = $this->cliente->apellido;
        //     $this->tipoCalle = $this->cliente->tipoCalle;
        //     $this->calle = $this->cliente->calle;
        //     $this->numero = $this->cliente->numero;
        //     $this->direccionAdicional1 = $this->cliente->direccionAdicional1;
        //     $this->direccionAdicional2 = $this->cliente->direccionAdicional2;
        //     $this->direccionAdicional3 = $this->cliente->direccionAdicional3;
        //     $this->codigoPostal = $this->cliente->codigoPostal;
        //     $this->ciudad = $this->cliente->ciudad;
        //     $this->nif = $this->cliente->nif;
        //     $this->tlf1 = $this->cliente->tlf1;
        //     $this->tlf2 = $this->cliente->tlf2;
        //     $this->tlf3 = $this->cliente->tlf3;
        //     $this->email1 = $this->cliente->email1;
        //     $this->email2 = $this->cliente->email2;
        //     $this->email3 = $this->cliente->email3;
        //     $this->confPostal = $this->cliente->confPostal;
        //     $this->confEmail = $this->cliente->confEmail;
        //     $this->confSms = $this->cliente->confSms;
        // }else {
        //     $this->id_cliente = 0;
        // }

        dd($id);
        $this->clienteSeleccionado = Cliente::find($id);
        $this->emit('rerender');
    }

    // Al hacer submit en el formulario
    public function submitClienteNuevo()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'apellido' => 'required',
                'tipoCalle' => 'required',
                'calle' => 'required',
                'numero' => 'required',
                'codigoPostal' => 'required',
                'ciudad' => 'required',
                'nif' => 'required',
                'tlf1' => 'required',
                'email1' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellido.required' => 'Los protagonistas son obligatorio.',
                'tipoCalle.required' => 'La cantidad de niños es obligatoria.',
                'calle.required' => 'El nombre de usuario es obligatorio.',
                'numero.required' => 'La contraseña es obligatoria.',
                'codigoPostal.required' => 'El lugar es obligatorio.',
                'ciudad.required' => 'La localidad es obligatoria.',
                'nif.required' => 'El Nif es obligatorio.',
                'tlf1.required' => 'El telefono es obligatorio.',
                'email1.required' => 'El email es obligatorio.',

            ]
        );

        // Guardar datos validados
        $clienteSave = Cliente::create(array_merge(
            $validatedData,
            [
                "trato" => $this->trato,
                "direccionAdicional1" => $this->direccionAdicional1,
                "direccionAdicional2" => $this->direccionAdicional2,
                "direccionAdicional3" => $this->direccionAdicional3,
                "tlf2" => $this->tlf2,
                "tlf3" => $this->tlf3,
                "email2" => $this->email2,
                "email3" => $this->email3,
                "confPostal" => $this->confPostal,
                "confEmail" => $this->confEmail,
                "confSms" => $this->confSms,
            ]
        ));


        // Alertas de guardado exitoso-
        if ($clienteSave) {
            $this->alert('success', '¡Usuario registrado correctamente!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'showCancelButton' => true,
                'onConfirmed' => 'confirmed',
                'onCancel' => 'verContrato',
                'confirmButtonText' => 'Seguir editando',
                'cancelButtonText' => 'Ver contrato',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }
    }

    public function setupServicioEventoList($serviciosListDia)
    {
        foreach ($serviciosListDia as $dia => $servicioEventoList) {
            foreach ($servicioEventoList as $key => $servicioEvento) {
                $programas = [];
                $programas = Programa::where("id_servicioEvento", $servicioEvento["id"])->get()->toArray();
                // dd($programas);
                foreach ($programas as $i => $programa) {
                    $this->type = substr($programa["comienzoEvento"], 0, 4);
                    $programas[$i]["comienzoEvento"] = substr($programa["comienzoEvento"], 0, 5);
                    $programas[$i]["comienzoMontaje"] = substr($programa["comienzoMontaje"], 0, 5);

                    $programas[$i]["costoDesplazamiento"] = is_null($programas[$i]["costoDesplazamiento"]) ? 0 : $programas[$i]["costoDesplazamiento"];
                }
                $servicioEvento["programas"] = $programas;
                $horaMontaje = substr($servicioEvento["comienzoMontaje"], 0, 5);
                $servicioEvento["comienzoMontaje"] = $horaMontaje;
                $horaInicio = substr($servicioEvento["horaInicio"], 0, 5);
                $servicioEvento["horaInicio"] = $horaInicio;


                $servicioEventoList[$key] =  $servicioEvento;
                $this->serviciosListDia[$dia] = $servicioEventoList;
                $this->refreshEndTime($key, $dia);
                $this->addProgramServiceField($servicioEvento["numMonitores"], $key, $dia);
            }
        }
    }


    public function setUpServiceForm($i)
    {
        $dia = $this->dias[$i];
        if (empty($this->serviciosListDia[$dia])) {
            $this->addServiceField($dia);
        }
    }


    //Selecciona un cliente y rellena los campos con los datos del mismo
    public function selectClient($solicitante)
    {
        $cliente = $this->clientes->where("id", $solicitante)->first();
        $this->id_cliente = $solicitante;
        $this->trato = $cliente->trato;
        $this->nombre = $cliente->nombre;
        $this->apellido = $cliente->apellido;
        $this->tipoCalle = $cliente->tipoCalle;
        $this->calle = $cliente->calle;
        $this->numero = $cliente->numero;
        $this->direccionAdicional1 = $cliente->direccionAdicional1;
        $this->direccionAdicional2 = $cliente->direccionAdicional2;
        $this->direccionAdicional3 = $cliente->direccionAdicional3;
        $this->codigoPostal = $cliente->codigoPostal;
        $this->ciudad = $cliente->ciudad;
        $this->nif = $cliente->nif;
        $this->tlf1 = $cliente->tlf1;
        $this->tlf2 = $cliente->tlf2;
        $this->tlf3 = $cliente->tlf3;
        $this->email1 = $cliente->email1;
        $this->email2 = $cliente->email2;
        $this->email3 = $cliente->email3;
        $this->emit("refreshComponent");
    }

    //Selecciona un cliente y rellena los campos con los datos del mismo
    public function selectCliente()
    {
        // $cliente = $this->clientes->where("id", $this->id_cliente)->first();
        // $this->trato = $cliente->trato;
        // $this->nombre = $cliente->nombre;
        // $this->apellido = $cliente->apellido;
        // $this->tipoCalle = $cliente->tipoCalle;
        // $this->calle = $cliente->calle;
        // $this->numero = $cliente->numero;
        // $this->direccionAdicional1 = $cliente->direccionAdicional1;
        // $this->direccionAdicional2 = $cliente->direccionAdicional2;
        // $this->direccionAdicional3 = $cliente->direccionAdicional3;
        // $this->codigoPostal = $cliente->codigoPostal;
        // $this->ciudad = $cliente->ciudad;
        // $this->nif = $cliente->nif;
        // $this->tlf1 = $cliente->tlf1;
        // $this->tlf2 = $cliente->tlf2;
        // $this->tlf3 = $cliente->tlf3;
        // $this->email1 = $cliente->email1;
        // $this->email2 = $cliente->email2;
        // $this->email3 = $cliente->email3;
        // $this->emit("refreshComponent");

        $this->clienteSeleccionado = Cliente::find($this->id_cliente);
    }

    public function primerPaso()
    {
        // dd($this->clienteSeleccionado);

        if ($this->clienteSeleccionado == null) {
            return $this->alert('error', '¡No se ha seleccionado a ningun cliente!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        $this->currentStep = 2;
    }

    public function segundoPaso()
    {
        // dd($this->clienteSeleccionado);
        if ($this->diaEvento == null) {
            return $this->alert('error', '¡No se ha selccionado ningun dia de comienzo del evento!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }
        if ($this->diaFinal == null) {
            return $this->alert('error', '¡No se ha selccionado ningun dia de finalizacion del evento!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoNombre == null) {
            return $this->alert('error', '¡El campo de Evento no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoProtagonista == null) {
            return $this->alert('error', '¡El campo de Protagonistas no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }
        if ($this->eventoNiños == null) {
            return $this->alert('error', '¡El campo de Numero de niños no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoContacto == null) {
            return $this->alert('error', '¡El campo de Contacto no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoParentesco == null) {
            return $this->alert('error', '¡El campo de Parentesco no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoLugar == null) {
            return $this->alert('error', '¡El campo de Lugar no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoTelefono == null) {
            return $this->alert('error', '¡El campo de Telefono no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        if ($this->eventoLocalidad == null) {
            return $this->alert('error', '¡El campo de Localidad no puede estar vacio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }


        $this->currentStep = 3;
    }
    public function tercerPaso()
    {
        $this->currentStep = 4;
    }

    //Crea un cliente o lo actualiza si existe
    public function submitClient()
    {

        $clientValidate = $this->validate(
            [
                'nombre' => 'required',
                'apellido' => 'required',
                'tipoCalle' => 'required',
                'calle' => 'required',
                'nif' => 'required',
                'numero' => 'required',
                'codigoPostal' => 'required',
                'ciudad' => 'required',
                'tlf1' => 'required',
                'email1' => 'required',

            ],
            // Mensajes de error
            [
                'nombre.required' => "El nombre es obligatorio",
                'apellido.required' => "Los apellidos son obligatorios",
                'tipoCalle.required' => "El tipo de calle es obligatorio",
                'calle.required' => "La calle es obligatoria",
                'nif.required' => "El NIF/DNI es obligatorio",
                'numero.required' => "El numero es obligatorio",
                'codigoPostal.required' => "El código postal es obligatorio",
                'ciudad.required' => "La ciudad es obligatoria",
                'tlf1.required' => "El telefono es obligatorio",
                'email1.required' => "El email es obligatorio",
            ]
        );

        if ($this->clienteExistente) {
            $this->clienteIsSave = Cliente::where("id", $this->id_cliente)->update(array_merge(
                $clientValidate,
                [
                    "trato" => $this->trato,
                    "direccionAdicional1" => $this->direccionAdicional1,
                    "direccionAdicional2" => $this->direccionAdicional2,
                    "direccionAdicional3" => $this->direccionAdicional3,
                    "tlf2" => $this->tlf2,
                    "tlf3" => $this->tlf3,
                    "email2" => $this->email2,
                    "email3" => $this->email3,
                    "confPostal" => $this->confPostal,
                    "confEmail" => $this->confEmail,
                    "confSms" => $this->confSms,
                ]
            ));
            if ($this->clienteIsSave) {
                $this->editandoCliente++;
                $this->clienteIsSave = $this->id_cliente;
            }
        } else {
            $this->clienteIsSave = Cliente::create(array_merge(
                $clientValidate,
                [
                    "trato" => $this->trato,
                    "direccionAdicional1" => $this->direccionAdicional1,
                    "direccionAdicional2" => $this->direccionAdicional2,
                    "direccionAdicional3" => $this->direccionAdicional3,
                    "tlf2" => $this->tlf2,
                    "tlf3" => $this->tlf3,
                    "email2" => $this->email2,
                    "email3" => $this->email3,
                    "confPostal" => $this->confPostal,
                    "confEmail" => $this->confEmail,
                    "confSms" => $this->confSms,
                ]
            ));
        }
        if ($this->clienteIsSave) {
            $this->id_cliente = $this->clienteIsSave;
            $this->alert('success', 'Cliente actualizado correctamente!', [
                'position' => 'center',
                'timer' => 1500,
                'toast' => false,
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }
    }

    //Crea un evento en la base de datos
    public function submitEvento()
    {
        $diaEvento = Carbon::parse($this->diaEvento);
        $diaFin = Carbon::parse($this->diaFinal);

        if ($diaEvento > $diaFin) {
            $this->diaFinal = $this->diaEvento;
        }
        $eventValidate = $this->validate(
            [
                'eventoNombre' => 'required',
                'eventoProtagonista' => 'required',
                'eventoNiños' => 'required',
                'eventoContacto' => 'required',
                'eventoParentesco' => 'required',
                'eventoTelefono' => 'required',
                'eventoLugar' => 'required',
                'eventoLocalidad' => 'required',
                'diaEvento' => 'required',
                'diaFinal' => "required"

            ],
            // Mensajes de error
            [
                'eventoNombre.required' => "El nombre del evento es obligatorio",
                'eventoProtagonista.required' => "Los protagonistas son obligatorios",
                'eventoNiños.required' => "La cantidad de niños es obligatoria",
                'eventoContacto.required' => "El nombre del contacto es obligatorio",
                'eventoParentesco.required' => "El parentesco es obligatorio",
                'eventoTelefono.required' => "El telefono es obligatorio",
                'eventoLugar.required' => "El lugar es obligatorio",
                'eventoLocalidad.required' => "La localidad es obligatoria",
                'diaEvento.required' => "El día es obligatorio",
                'diaFinal.required' => "El día final es obligatorio",
            ]
        );

        $this->eventoIsSave = Evento::create($eventValidate);
        event(new \App\Events\LogEvent(Auth::user(), 11, $this->eventoIsSave->id));

        if ($this->eventoIsSave) {
            $this->submit();
        }
    }

    public function getDias()
    {

        $this->dias = [];
        if ($this->diaEvento != null && $this->diaFinal != null) {
            $period = CarbonPeriod::create($this->diaEvento, $this->diaFinal);

            foreach ($period as $i => $date) {
                $this->dias[$i] = $date->format("Y-m-d");
                $this->serviciosListDia[$this->dias[$i]] = [];
            }
            $lenght = count($this->dias);
            $servListLength = count($this->serviciosListDia);
            if ($lenght < $servListLength) {

                array_splice($this->serviciosListDia, $lenght);
            }

            $this->dia = $this->dias[0];
        }
    }

    public function addServiceFieldFromPack()
    {
        $servicios = Servicio::where("id_pack", $this->pack)->get();
        $pack = ServicioPack::where("id", $this->pack)->first();
        // dd($servicios);
        $servicioEventoList = $this->serviciosListDia[$this->dia];


        foreach ($servicios as $index => $servicio) {
            // dd($index);
            $serv = [
                'id_servicio' => $servicio->id,
                'horaInicio' => '00:00',
                'tiempo' => 0,
                'numMonitores' => $servicio->minMonitor,
                "id_evento" => $this->id_evento,
                "dia" => $this->dia,
                "importe" => $servicio->precioBase,
                "descuento" => 0,
                "importeBase" => $servicio->precioBase,
                "horaFin" => "00:00",
                "programas" => [],
                "comienzoMontaje" => "00:00",
                "tiempoDesmontaje" => $servicio->tiempoDesmontaje,
                "tiempoMontaje" => $servicio->tiempoMontaje,
                "pack" => $pack->nombre,
            ];
            if ($servicioEventoList[0]["id_servicio"] === 0 && $index == 0) {
                $servicioEventoList[0] = $serv;
            } else {
                array_push($servicioEventoList, $serv);
            }
        }
        $this->serviciosListDia[$this->dia] = $servicioEventoList;
        // dd($servicioEventoList);

        foreach ($servicioEventoList as $index => $servicio) {
            // dd($servicio);
            $this->addProgramServiceField($servicio["numMonitores"], $index);
        }





        // $this->programas[count($this->servicioEventoList)] = [];
    }
    public function getImporteBaseServ($servicio)
    {
        $minMonitores = $servicio->minMonitor;
        $importeBase = $servicio->precioBase + ($minMonitores * $servicio->precioMonitor);
        return $importeBase;
    }

    //Servicios
    //Rellena los valores por defecto de un servicio a la hora de añadirlos
    public function setDefaultServicios($key, $id)
    {
        $this->key = $key;

        // dd( $this->serviciosListDia);
        $serviciosDiaList = $this->serviciosListDia;
        $serviciosDia = $serviciosDiaList[$this->dia];


        $servicio = $this->servicios->find($id);
        $servicioEvento["precioMonitor"] = $servicio->precioMonitor;
        $minMonitores = $servicio->minMonitor;
        $importeBase = $this->getImporteBaseServ($servicio);
        $tiempoMontaje = $servicio->tiempoMontaje;
        $tiempoDesmontaje = $servicio->tiempoDesmontaje;


        $serviciosDia[$key] = array(
            'id_servicio' => $id,
            'horaInicio' => '00:00',
            'tiempo' => 1,
            'numMonitores' => $minMonitores,
            "id_evento" => $this->id_evento,
            "dia" => $this->dia,
            "importe" => $importeBase,
            "descuento" => 0,
            "importeBase" => $importeBase,
            "horaFin" => "00:00",
            "programas" => [],
            "comienzoMontaje" => "00:00",
            "tiempoDesmontaje" => $tiempoDesmontaje,
            "tiempoMontaje" => $tiempoMontaje,
        );

        $serviciosDiaList[$this->dia] = $serviciosDia;
        $this->serviciosListDia = $serviciosDiaList;

        $this->addProgramServiceField($minMonitores, count($serviciosDia) - 1);

        $this->getTotalPrice();
    }


    public function servicioMonitores($key, $id)
    {
        $this->servicioEvento = $this->servicioEventoList[$key];
        $minMonitores = $this->servicios->find($id)->minMonitor;
        $this->minMonitores = $this->servicios->find($id)->minMonitor;

        $servicioEvento["numMonitores"] = $this->servicios->find($id)->minMonitor;

        $this->numMonitores = $servicioEvento["numMonitores"];

        return $minMonitores;
    }

    public function nombreMonitor(int $id)
    {
        $nombreCompleto =  sprintf("%s %s", $this->monitores->find($id)->nombre,  $this->monitores->find($id)->apellidos);
        return $nombreCompleto;
    }

    public function checkTime($key, $i)
    {
        $servicioEvento = $this->servicioEventoList[$key];
        $programa = $servicioEvento["programas"][$i];
        $hora = $programa["horas"];

        if (!is_numeric($hora) || $hora <= 0) {
            $hora = 1;
        }
        $servicioEvento["programas"][$i]["horas"] = $hora;
        $this->servicioEventoList[$key] = $servicioEvento;
    }

    //Calcula y aplica el descuento de un servicio
    public function applyServiceDiscount($key)
    {
        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$key];

        $servicio = $this->servicios->find($servicioEvento["id_servicio"]);
        $numMonitores = $servicioEvento["numMonitores"];
        $importeBase = $servicioEvento["importeBase"];
        if ($importeBase == 0) {
            $importeBase = $servicio->precioBase + ($numMonitores * $servicio->precioMonitor);
        }

        if (
            $servicioEvento["descuento"] === null ||
            !is_numeric($servicioEvento["descuento"]) ||
            $servicioEvento["descuento"] < 0
        ) {
            $this->serviciosListDia[$this->dia][$key]["descuento"] = 0;
            $servicioEvento["descuento"] = 0;
        }
        if ($servicioEvento["descuento"] > 100) {
            $this->serviciosListDia[$this->dia][$key]["descuento"] = 100;
            $servicioEvento["descuento"] = 100;
        }


        $descuentoPC = abs($servicioEvento["descuento"]) / 100;
        $descuento = $importeBase * $descuentoPC;

        $importe = $importeBase - $descuento;

        $servicioEvento["importe"] = $importe;

        $serviciosDia[$key] = $servicioEvento;

        $this->serviciosListDia[$this->dia] = $serviciosDia;

        $this->getTotalPrice();
    }

    public function refreshNumMonitores($key)
    {
        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servEv = $serviciosDia[$key];
        $serv = $servEv["id_servicio"];
        $minMonitores = $this->servicios->find($serv)->minMonitor;
        $numMonitores = $servEv["numMonitores"];
        if (!is_numeric($numMonitores) || $numMonitores < $minMonitores) {
            $this->type = !is_numeric($numMonitores);
            $this->lowerN = $numMonitores < $minMonitores;
            $numMonitores = $minMonitores;
            $servEv["numMonitores"] = $minMonitores;
        }
        $serviciosDia[$key] = $servEv;
        $this->serviciosListDia[$this->dia] = $serviciosDia;
        $this->addProgramServiceField($numMonitores, $key, $this->dia);
        $this->applyServiceDiscount($key);
    }

    public function refreshEndTime($key)
    {
        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$key];
        $horaInicio = $servicioEvento["horaInicio"];
        $tiempo  = $servicioEvento["tiempo"];


        $servicioEvento["horaFin"] = $this->horaFinalizacion($servicioEvento);

        $serviciosDia[$key] = $servicioEvento;

        $this->serviciosListDia[$this->dia] = $serviciosDia;


        $comienzoMontaje = $servicioEvento["comienzoMontaje"];
        $tiempo  = $servicioEvento["tiempoMontaje"];
        $minMontaje = strtotime("$comienzoMontaje + $tiempo minutes");

        if (strtotime($horaInicio) < $minMontaje) {
            $this->refreshMountTime($key, $this->dia);
        }
    }

    public function updateComienzoMontajePrograma($keyServicio)
    {

        $servicioEvento = $this->servicioEventoList[$keyServicio];
        $horaMontaje = $servicioEvento["comienzoMontaje"];

        foreach ($servicioEvento["programas"] as $index => $programa) {
            $programa["comienzoMontaje"] = $horaMontaje;
            $servicioEvento["programas"][$index] = $programa;
        }

        $this->servicioEventoList[$keyServicio] = $servicioEvento;
    }


    public function refreshMountTime($key)
    {
        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$key];
        $horaInicio = $servicioEvento["horaInicio"];
        $tiempo  = $servicioEvento["tiempoMontaje"];
        $horaMontaje = $servicioEvento["comienzoMontaje"];

        if (strtotime("$horaInicio - $tiempo minutes") < strtotime($horaMontaje)) {
            $horaMontaje  = date("H:i", strtotime("$horaInicio - $tiempo minutes"));

            $servicioEvento["comienzoMontaje"] = $horaMontaje;

            $serviciosDia[$key] = $servicioEvento;
            $this->serviciosListDia[$this->dia]  = $serviciosDia;
        }
    }


    public function selectorCliente()
    {
        $this->clienteExistente = true;
        $this->emit('clienteExistenteEmit');
    }


    //Saca el nombre de un servicio
    public function servicioNombre($id)
    {
        return $this->servicios->find($id)->nombre;
    }

    public function selectedCompanyItem($item)
    {
        if ($item) {
            $this->cliente = $this->clientes->find($item);
            if ($this->cliente) {
                $this->trato = $this->cliente->trato;
                $this->nombre = $this->cliente->nombre;
                $this->apellido = $this->cliente->apellido;
                $this->tipoCalle = $this->cliente->tipoCalle;
                $this->calle = $this->cliente->calle;
                $this->numero = $this->cliente->numero;
                $this->direccionAdicional1 = $this->cliente->direccionAdicional1;
                $this->direccionAdicional2 = $this->cliente->direccionAdicional2;
                $this->direccionAdicional3 = $this->cliente->direccionAdicional3;
                $this->codigoPostal = $this->cliente->codigoPostal;
                $this->ciudad = $this->cliente->ciudad;
                $this->nif = $this->cliente->nif;
                $this->tlf1 = $this->cliente->tlf1;
                $this->tlf2 = $this->cliente->tlf2;
                $this->tlf3 = $this->cliente->tlf3;
                $this->email1 = $this->cliente->email1;
                $this->email2 = $this->cliente->email2;
                $this->email3 = $this->cliente->email3;
                $this->confPostal = $this->cliente->confPostal;
                $this->confEmail = $this->cliente->confEmail;
                $this->confSms = $this->cliente->confSms;
            } else {
                $this->id_cliente = 0;
            }
        }
    }


    //Calcula la hora final del servicio a partir de su hora de inicio y su duracion
    public function horaFinalizacion($servicioEvento)
    {
        $horaInicio = $servicioEvento["horaInicio"];
        $tiempo  = $servicioEvento["tiempo"];
        $hora = date("H:i:s", strtotime("$horaInicio + $tiempo hours"));
        // $hora = $servicioEvento->horaInicio->modify("+$servicioEvento->tiempo hours");
        return $hora;
    }


    public function addServiceField()
    {

        $servicioEventoList = $this->serviciosListDia[$this->dia];
        $servicioEventoList[count($servicioEventoList)] = array(
            'id_servicio' => 0,
            'horaInicio' => '00:00',
            'tiempo' => 0,
            'numMonitores' => 0,
            "id_evento" => $this->id_evento,
            "dia" => $this->dia,
            "importe" => 0,
            "descuento" => 0,
            "importeBase" => 0,
            "horaFin" => "00:00",
            "programas" => [],
            "comienzoMontaje" => "00:00",
            "tiempoMontaje" => 0,
            "tiempoDesmontaje" => 0,
        );

        $this->serviciosListDia[$this->dia] = $servicioEventoList;

        // $this->programas[count($this->servicioEventoList)] = [];
    }



    public function addProgramServiceField($numMonitores, $index)
    {

        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$index];
        $id = $servicioEvento["id_servicio"];

        $programas = [];

        if ($id > 0) {
            $precioMonitor = $this->servicios->find($id)->precioMonitor;
            $i = 0;
            if (!empty($servicioEvento["programas"])) {
                $i = count($servicioEvento["programas"]);
            }

            $programas = $servicioEvento["programas"];

            for ($i; $i < $numMonitores; $i++) {

                $programas[$i] = array(
                    "id_monitor" => 0,
                    "id_servicioEvento" => 0,
                    "dia" => $this->diaEvento,
                    "precioMonitor" => $precioMonitor,
                    "costoDesplazamiento" => 0,
                    "comienzoMontaje" => $servicioEvento["comienzoMontaje"],
                    "comienzoEvento" => $servicioEvento["horaInicio"],
                    "horas" => $servicioEvento["tiempo"],
                    "tiempoDesmontaje" => 0,
                    // "montador"=> false,
                );
            }

            if ($numMonitores < count($servicioEvento["programas"])) {

                $this->currentProgram = array_slice($servicioEvento["programas"], 0, $numMonitores);


                $programas = array_slice($servicioEvento["programas"], 0, $numMonitores);
            }

            $servicioEvento["importeBase"] += $precioMonitor * $numMonitores;
            $servicioEvento["importe"] += $precioMonitor * $numMonitores;
            $servicioEvento["programas"] = $programas;
            $serviciosDia[$index] =  $servicioEvento;
            $this->serviciosListDia[$this->dia] =  $serviciosDia;
            // dd($this->serviciosListDia);
            $this->getTotalPrice();
        }
    }

    public function sumarCostoMonitor($servicio, $key, $i)
    {
        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$key];
        $serv = $this->servicios->find($servicio);
        $baseMonitor = $serv->precioMonitor;


        $this->importeBase = $servicioEvento["importeBase"] + $servicioEvento["programas"][$i]["precioMonitor"] - $baseMonitor;
        // $servicioEvento["importeBase"] += $servicioEvento["programas"][$i]["precioMonitor"] - $baseMonitor;

        $this->servicioEvento = $servicioEvento;
        $this->baseMonitor = $baseMonitor;
        $this->servicioEvento = $servicioEvento;

        $precioMonitor = $servicioEvento["programas"][$i]["precioMonitor"];
        $precioDesplazamiento = $servicioEvento["programas"][$i]["costoDesplazamiento"];

        if ($precioMonitor < $baseMonitor) {
            $servicioEvento["programas"][$i]["precioMonitor"] = $baseMonitor;
            $precioMonitor = $baseMonitor;
        }

        if ($precioDesplazamiento < 0) {
            $servicioEvento["programas"][$i]["costoDesplazamiento"] = 0;
            $precioDesplazamiento = 0;
        } else {
            $precioDesplazamiento = $servicioEvento["programas"][$i]["costoDesplazamiento"];
        }


        $this->precioMonitor = $precioMonitor;

        // $descuentoPC = abs($servicioEvento["descuento"]) / 100;

        $servicioEvento["importeBase"] =
            $this->getImporteBaseServ($serv) +
            $servicioEvento["programas"][$i]["precioMonitor"] - $baseMonitor +
            $precioDesplazamiento;


        // $descuento =  $servicioEvento["importeBase"] * $descuentoPC;


        $servicioEvento["importe"] = $servicioEvento["importeBase"];
        // $servicioEvento["importe"] = $servicioEvento["importeBase"] - $descuento;



        $serviciosDia[$key] = $servicioEvento;
        $this->serviciosListDia[$this->dia] = $serviciosDia;



        $this->applyServiceDiscount($key);
    }


    public function removeServicio($key)
    {

        //Miro si existe en la bd y si es asi lo borro

        if (isset($this->serviciosListDia[$this->dia][$key]["id"])) {
            ServicioEvento::find($this->serviciosListDia[$this->dia][$key]["id"])->delete();
        }

        //Lo quito de la lista de servicios
        unset($this->serviciosListDia[$this->dia][$key]);


        $this->getTotalPrice();
    }

    public function crearClientes()
    {
        session([
            'datos' => [
                'nPresupuesto' => $this->nPresupuesto,
                'gestor_id' => $this->gestor_id,
                'fechaEmision' => $this->fechaEmision,
                'categoria_evento_id' => $this->categoria_evento_id,
                'estado' => $this->estado,
                'fechaVencimiento' => $this->fechaVencimiento,
            ]
        ]);
        return Redirect::to(route("clientes.create-from-budget"));
    }
    public function crearTipoEvento()
    {
        session([
            'datos' => [
                'nPresupuesto' => $this->nPresupuesto,
                'gestor_id' => $this->gestor_id,
                'fechaEmision' => $this->fechaEmision,
                'categoria_evento_id' => $this->categoria_evento_id,
                'estado' => $this->estado,
                'fechaVencimiento' => $this->fechaVencimiento,
            ]
        ]);
        return Redirect::to(route("tipo-evento.create-from-budget"));
    }

    //Calcula el precio total teniendo en cuenta el precio de la lista de los servicios y aplica un descuento si es necesario
    public function getTotalPrice()
    {
        $total = 0;
        if ($this->adelanto < 0 || $this->adelanto === null || $this->adelanto === "") {
            $this->adelanto = 0;
        }
        foreach ($this->serviciosListDia as $dia => $servicioEventoList) {
            foreach ($servicioEventoList as $servicio) {
                $total += $servicio["importe"];
            }
            if ($this->addDiscount) {
                $descTotal = $total * (abs($this->descuento) / 100);
                $this->precioFinal = $total - $descTotal;
                $this->entregaDiscount = $this->precioFinal * ($this->adelanto / 100);
            } else {
                $this->precioFinal = $total;
                $this->precioBase = $total;
                $this->entrega = $total *  ($this->adelanto / 100);
            }
        }
    }

    //alterna entre activar y descativar el descuento total
    public function allowDisabDisc()
    {
        if ($this->addDiscount) {
            $this->descuento = 0;
            $this->addDiscount = !$this->addDiscount;
        } else {
            $this->addDiscount = !$this->addDiscount;
        }

        $this->getTotalPrice();
    }

    //Trigger para crear cliente
    public function crearCliente()
    {
        $this->addCliente = true;
    }

    public function checkLocalidad()
    {
        if ($this->eventoLocalidad != null) {
            $this->dispatchBrowserEvent('getLocalidad');
        }
    }

    public function uncheckClient()
    {

        $this->clienteIsSave = false;
        $this->setupClient($this->id_cliente);
    }

    public function uncheckEvent()
    {
        $this->eventoIsSave = false;
        $this->setupEvento($this->id_evento);
    }

    public function checkEvent()
    {
        $this->eventoIsSave = true;
    }

    //Trigger para añadir observaciones
    public function allowObs()
    {
        $this->addObservaciones = true;
    }

    //Pasar la lista de servicios a una lista de modelos de servicio NO TESTED YET
    public function submitServicioList()
    {
        $servicios = [];
        $programas = [];
        $i = 0;
        foreach ($this->serviciosListDia as $servicioEventoList) {

            foreach ($servicioEventoList as $index => $servicio) {


                // $programas[$i + $index] = $servicio["programas"];
                $programa = $servicio["programas"];
                unset($servicio["programas"]);
                unset($servicio["horaFin"]);
                $exist = isset($servicio["id"]);
                if ($exist) {
                    ServicioEvento::find($servicio["id"])->update($servicio);
                    $serv = ServicioEvento::find($servicio["id"])->toArray();
                } else {
                    $serv = ServicioEvento::create($servicio)->toArray();
                }

                if ($serv) {
                    $servicios[$index] = $serv;
                    // dd($programa);
                    $programa[0]["id_servicioEvento"] = $serv["id"];
                    // dd($programa);
                    $programas[$i + $index] = $programa;
                }
            }
            $i++;
        }
        // dd($programas);
        $this->servicio = $servicios;
        $this->programas = $programas;
        return $servicios;
    }

    //Pasar la lista de servicios a una lista de modelos de servicio NO TESTED YET
    public function submitProgramas($servicios)
    {
        $programasConf = [];
        foreach ($this->programas as $index => $programas) {
            foreach ($programas as $i => $programa) {
                // unset($programa["montador"]);
                // dd($this->programas);
                // $programa["id_servicioEvento"] = $servicios[$index]["id"];

                if (array_key_exists("id", $programa)) {
                    $prg = Programa::find($programa["id"])->update($programa);
                } else {
                    $prg = Programa::create($programa);
                }

                if ($prg) {
                    $programasConf[$index] = $prg;
                }
            }
        }
        return $programasConf;
    }
    // Al hacer submit en el formulario
    public function submit()
    {
        $this->precioBase = $this->precioFinal;
        $this->precioFinal = $this->precioBase - $this->descuento;
        $this->id_evento = $this->eventoIsSave->id;

        $validatedData = $this->validate(
            [
                'nPresupuesto' => 'required',
                'fechaEmision' => 'required',
                'fechaVencimiento' => 'nullable',
                'categoria_evento_id' => 'required',
                'id_evento' => 'required',
                'id_cliente' => 'required',
                'precioBase' => 'required',
                'precioFinal' => 'required',
                'descuento' => 'nullable',
                'adelanto' => 'nullable',
                'estado' => 'required',
                'observaciones' => 'nullable',
                'gasoilDistancia' => 'nullable',
                'gestor_id' => 'nullable'

            ],
            // Mensajes de error
            [
                'fechaEmision' => 'El número de presupuesto es obligatorio.',
                'id_evento.required' => 'La fecha de emision es obligatoria.',
                'id_cliente.required' => 'El alumno es obligatorio.',
                'precioBase.required' => 'El curso es obligatorio.',
                'precioFinal.required' => 'Los detalles son obligatorios',

                'estado.required' => 'El estado es obligatorio',
            ]
        );



        // Guardar datos validados
        $presupuesosSave = Presupuesto::create($validatedData);
        $this->identificador = $presupuesosSave->id;

        event(new \App\Events\LogEvent(Auth::user(), 3, $presupuesosSave->id));

        foreach ($this->listaServicios as $servicio) {

            $presupuesosSave->servicios()->attach(
                $servicio['id'],
                [
                    'numero_monitores' => $servicio['numero_monitores'],
                    'precio_final' => $servicio['precioFinal'],
                    'tiempo' => $servicio['tiempo'],
                    'tiempo_montaje' => $servicio['tiempo_montaje'],
                    'tiempo_desmontaje' => $servicio['tiempo_desmontaje'],
                    'hora_inicio' => $servicio['hora_inicio'],
                    'hora_finalizacion' => $servicio['hora_finalizacion'],
                    'hora_montaje' => $servicio['hora_montaje'],
                    'sueldo_monitores' => json_encode($servicio['sueldo_monitores']),
                    'id_monitores' => json_encode($servicio['id_monitores']),
                    'gasto_gasoil' => json_encode($servicio['gasto_gasoil']),
                    'pago_pendiente' => json_encode($servicio['sueldo_monitores']),
                ]
            );
        }

        foreach ($this->listaPacks as $pack) {
            $presupuesosSave->packs()->attach($pack['id'], [
                'numero_monitores' => json_encode($pack['numero_monitores']),
                'precio_final' => $pack['precioFinal'],
                'tiempos' => json_encode($pack['tiempos']),
                'horas_inicio' => json_encode($pack['horas_inicio']),
                'horas_finalizacion' => json_encode($pack['horas_finalizacion']),
                'tiempos_montaje' => json_encode($pack['tiempos_montaje']),
                'tiempos_desmontaje' => json_encode($pack['tiempos_desmontaje']),
                'horas_montaje' => json_encode($pack['horas_montaje']),
                'sueldos_monitores' => json_encode($pack['sueldos_monitores']),
                'id_monitores' => json_encode($pack['id_monitores']),
                'gastos_gasoil' => json_encode($pack['gastos_gasoil']),
                'pagos_pendientes' => json_encode($pack['sueldos_monitores']),
            ]);
        }

        // Guardar datos validados
        $contratoSave = Contrato::create([
            "id_presupuesto" => $presupuesosSave->id,
            'metodoPago' => $this->metodoPago,
            'cuentaTransferencia' => $this->cuentaTransferencia,
            'observaciones' => $this->observaciones,
            'authImagen' => $this->authImagen,
            'authMenores' => $this->authMenores,
            'dia' => $this->diaEvento,
        ]);
        $this->contrato_id = $contratoSave->id;

        event(new \App\Events\LogEvent(Auth::user(), 14, $contratoSave->id));



        // Alertas de guardado exitoso
        if ($presupuesosSave) {
            $this->alert('success', '¡Presupuesto registrado correctamente!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'Seguir editando',
                'showDenyButton' => true,
                'denyButtonText' => 'Ver contrato',
                'onDenied' => 'verContrato',
                'timer' => null,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del presupuesto!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }
    }
    public function cambioPrecioPack()
    {
        $pack = $this->packs->where('id', $this->pack_seleccionado)->first()->servicios()->get();
        $this->precioFinalPack = 0;
        foreach ($pack as $keyPack => $servicio) {
            if (isset($this->preciosMonitores[$keyPack])) {
                $this->preciosBasePack[$keyPack] = ($servicio->precioBase + ($this->preciosMonitores[$keyPack] * $servicio->precioMonitor));
            } else {
                $this->preciosMonitores[$keyPack] = $servicio->minMonitor;
                $this->preciosBasePack[$keyPack] = $servicio->precioBase;
                $this->preciosMonitores[$keyPack] = $servicio->minMonitor;
                try {
                    $this->tiemposMontajePack[$keyPack] = Carbon::createFromFormat('H:i:s', $servicio->tiempoMontaje)->format('H:i');
                } catch (\Exception $e) {
                    // Si falla, intenta con el formato 'H:i'
                    try {
                        $this->tiemposMontajePack[$keyPack] = Carbon::createFromFormat('H:i', $servicio->tiempoMontaje)->format('H:i');
                    } catch (\Exception $e) {
                    }
                }

                try {
                    $this->tiemposDesmontajePack[$keyPack] = Carbon::createFromFormat('H:i:s', $servicio->tiempoDesmontaje)->format('H:i');
                } catch (\Exception $e) {
                    // Si falla, intenta con el formato 'H:i'
                    try {
                        $this->tiemposDesmontajePack[$keyPack] = Carbon::createFromFormat('H:i', $servicio->tiempoDesmontaje)->format('H:i');
                    } catch (\Exception $e) {
                    }
                }

                try {
                    $this->tiemposPack[$keyPack] = Carbon::createFromFormat('H:i:s', $servicio->tiempoServicio)->format('H:i');
                } catch (\Exception $e) {
                    // Si falla, intenta con el formato 'H:i'
                    try {
                        $this->tiemposPack[$keyPack] = Carbon::createFromFormat('H:i', $servicio->tiempoServicio)->format('H:i');
                    } catch (\Exception $e) {
                    }
                }
            }
            $this->precioFinalPack += ($servicio->precioBase + ($this->preciosMonitores[$keyPack] * $servicio->precioMonitor));
        }
    }

    public function cambioTiempoPack()
    {
        if ($this->pack_seleccionado != 0) {
            $pack = $this->packs->where('id', $this->pack_seleccionado)->first()->servicios()->get();
            foreach ($pack as $keyPack => $servicio) {
                if ($this->eventoMontaje == 1) {
                    if (isset($this->tiemposMontajePack[$keyPack]) && isset($this->horasMontajePack[$keyPack])) {
                        $inicio = Carbon::createFromFormat('H:i', $this->horasMontajePack[$keyPack]);
                        $fin = Carbon::createFromFormat('H:i', $this->tiemposMontajePack[$keyPack]);
                        list($horas, $minutos) = explode(':', $fin->format('H:i'));
                        $this->horasInicioPack[$keyPack] = $inicio->addHours($horas)->addMinutes($minutos)->format('H:i');
                        $this->emit('refresh');
                    }
                    if (isset($this->tiemposPack[$keyPack]) && isset($this->horasInicioPack[$keyPack])) {
                        if (isset($this->tiemposDesmontajePack[$keyPack])) {
                            $inicio = Carbon::createFromFormat('H:i', $this->horasInicioPack[$keyPack]);
                            $medio = Carbon::createFromFormat('H:i', $this->tiemposDesmontajePack[$keyPack]);
                            $fin = Carbon::createFromFormat('H:i', $this->tiemposPack[$keyPack]);
                            list($horas, $minutos) = explode(':', $medio->format('H:i'));
                            list($horas2, $minutos2) = explode(':', $fin->format('H:i'));
                            $this->horasFinalizacionPack[$keyPack] = $inicio->addMinutes((int)$minutos)->addHours((int)$horas)->addMinutes((int)$minutos2)->addHours((int)$horas2)->format('H:i');
                            $this->emit('refresh');
                        } else {
                            $inicio = Carbon::createFromFormat('H:i', $this->horasInicioPack[$keyPack]);
                            $fin = Carbon::createFromFormat('H:i', $this->tiemposPack[$keyPack]);
                            list($horas, $minutos) = explode(':', $fin->format('H:i'));
                            $this->horasFinalizacionPack[$keyPack] = $inicio->addMinutes((int)$minutos)->addHours((int)$horas)->format('H:i');
                            $this->emit('refresh');
                        }
                    }
                } else {
                    if (isset($this->tiemposPack[$keyPack]) && isset($this->horasInicioPack[$keyPack])) {
                        $inicio = Carbon::createFromFormat('H:i', $this->horasInicioPack[$keyPack]);
                        $fin = Carbon::createFromFormat('H:i', $this->tiemposPack[$keyPack]);
                        list($horas, $minutos) = explode(':', $fin->format('H:i'));
                        $this->horasFinalizacionPack[$keyPack] = $inicio->addMinutes((int)$minutos)->addHours((int)$horas)->format('H:i');
                        $this->emit('refresh');
                    }
                }
            }
        } else {
            $this->alert('error', 'Selecciona un pack.');
            $this->numero_monitores = 0;
            $this->tiempo = 0;
            $this->hora_inicio = 0;
            $this->hora_finalizacion = 0;
            $this->precioFinalServicio = 0;
        }
    }

    public function addMonitorServicio($id1, $id2)
    {
        $this->listaServicios[$id1]['sueldo_monitores'][$id2] = $this->servicios->firstWhere('id', $this->listaServicios[$id1]['id'])->precioMonitor;
    }

    public function cambioPrecioServicio()
    {
        if ($this->servicio_seleccionado != 0) {
            $servicio = $this->servicios->where('id', $this->servicio_seleccionado)->first();
            $this->precioFinalServicio = ($servicio->precioBase + ($this->numero_monitores * $servicio->precioMonitor));
            $this->numero_monitores = $servicio->minMonitor;
            try {
                $this->tiempoMontaje = Carbon::createFromFormat('H:i:s', $servicio->tiempoMontaje)->format('H:i');
            } catch (\Exception $e) {
                // Si falla, intenta con el formato 'H:i'
                try {
                    $this->tiempoMontaje = Carbon::createFromFormat('H:i', $servicio->tiempoMontaje)->format('H:i');
                } catch (\Exception $e) {
                }
            }

            try {
                $this->tiempoDesmontaje = Carbon::createFromFormat('H:i:s',  $servicio->tiempoDesmontaje)->format('H:i');
            } catch (\Exception $e) {
                // Si falla, intenta con el formato 'H:i'
                try {
                    $this->tiempoDesmontaje = Carbon::createFromFormat('H:i', $servicio->tiempoDesmontaje)->format('H:i');
                } catch (\Exception $e) {
                }
            }

            try {
                $this->tiempo = Carbon::createFromFormat('H:i:s',  $servicio->tiempoServicio)->format('H:i');
            } catch (\Exception $e) {
                // Si falla, intenta con el formato 'H:i'
                try {
                    $this->tiempo = Carbon::createFromFormat('H:i', $servicio->tiempoServicio)->format('H:i');
                } catch (\Exception $e) {
                }
            }
        } else {
            $this->alert('error', 'Selecciona un servicio.');
            $this->numero_monitores = 0;
            $this->tiempo = 0;
            $this->hora_inicio = 0;
            $this->hora_finalizacion = 0;
            $this->precioFinalServicio = 0;
        }
    }

    public function asignarValorInicial($keyPack, $value)
    {
        $this->preciosMonitores[$keyPack] = $value;
        $this->cambioPrecioPack();
    }

    public function cambioTiempoServicio()
    {
        if ($this->servicio_seleccionado != 0) {
            if ($this->eventoMontaje == 1) {
                if (isset($this->tiempoMontaje) && isset($this->horaMontaje) && ($this->tiempoMontaje != 0) && ($this->horaMontaje != 0)) {
                    $inicio = Carbon::createFromFormat('H:i', $this->horaMontaje);
                    $fin = Carbon::createFromFormat('H:i', $this->tiempoMontaje);
                    list($horas, $minutos) = explode(':', $fin->format('H:i'));
                    $this->hora_inicio = $inicio->addHours($horas)->addMinutes($minutos)->format('H:i');
                    $this->emit('refresh');
                }
                if (isset($this->tiempo) && isset($this->hora_inicio) && ($this->tiempo != 0) && ($this->hora_inicio != 0)) {
                    if (isset($this->tiempoDesmontaje) && ($this->tiempoDesmontaje != 0)) {
                        $inicio = Carbon::createFromFormat('H:i', $this->hora_inicio);
                        $medio = Carbon::createFromFormat('H:i', $this->tiempoDesmontaje);
                        $fin = Carbon::createFromFormat('H:i', $this->tiempo);
                        list($horas, $minutos) = explode(':', $medio->format('H:i'));
                        list($horas2, $minutos2) = explode(':', $fin->format('H:i'));
                        $this->hora_finalizacion = $inicio->addMinutes((int)$minutos)->addHours((int)$horas)->addMinutes((int)$minutos2)->addHours((int)$horas2)->format('H:i');
                        $this->emit('refresh');
                    } else {
                        $inicio = Carbon::createFromFormat('H:i', $this->hora_inicio);
                        $fin = Carbon::createFromFormat('H:i', $this->tiemposPack[$keyPack]);
                        list($horas, $minutos) = explode(':', $fin->format('H:i'));
                        $this->hora_finalizacion = $inicio->addMinutes((int)$minutos)->addHours((int)$horas)->format('H:i');
                        $this->emit('refresh');
                    }
                }
            } else {
                if (isset($this->tiempo) && isset($this->hora_inicio) && ($this->tiempo != 0) && ($this->hora_inicio     != 0)) {
                    $inicio = Carbon::createFromFormat('H:i', $this->hora_inicio);
                    $fin = Carbon::createFromFormat('H:i', $this->tiempo);
                    list($horas, $minutos) = explode(':', $fin->format('H:i'));
                    $this->hora_finalizacion = $inicio->addMinutes((int)$minutos)->addHours((int)$horas)->format('H:i');
                    $this->emit('refresh');
                }
            }
        } else {
            $this->alert('error', 'Selecciona un servicio.', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
            $this->numero_monitores = 0;
            $this->tiempo = 0;
            $this->hora_inicio = 0;
            $this->hora_finalizacion = 0;
            $this->precioFinalServicio = 0;
        }
    }

    public function addPack()
    {
        if ($this->pack_seleccionado != 0) {
            $packId = $this->pack_seleccionado;
            $existe = Presupuesto::whereHas('packs', function ($query) use ($packId) {
                $query->where('servicio_packs.id', $packId);
            })
                ->where('fechaEmision', $this->diaEvento)
                ->exists();

            $fechaEvento = $this->diaEvento; // Fecha en la que deseas verificar el stock
            // Variable para rastrear si el stock se supera
            $stockSeSupera = false;
            // Obtener los artículos relacionados con el servicio
            foreach ($this->packs->where('id', $packId)->first()->servicios()->get() as $servicio) {
                $servicioId = $servicio->id;
                $articulosDelServicio = DB::table('servicio_articulo')
                    ->where('servicio_id', $servicioId)
                    ->get();
                // Iterar a través de los artículos del servicio y verificar el stock para cada uno
                foreach ($articulosDelServicio as $articuloDelServicio) {
                    $articuloId = $articuloDelServicio->articulo_id;

                    // Obtener la cantidad total utilizada de este artículo en la fecha indicada
                    $cantidadTotalUtilizada = DB::table('presupuestos')
                        ->join('servicio_presupuesto', 'presupuestos.id', '=', 'servicio_presupuesto.presupuesto_id')
                        ->join('servicios', 'servicio_presupuesto.servicio_id', '=', 'servicios.id')
                        ->join('servicio_articulo', 'servicios.id', '=', 'servicio_articulo.servicio_id')
                        ->where('presupuestos.fechaEmision', $fechaEvento)
                        ->where('servicio_articulo.articulo_id', $articuloId)
                        ->sum('servicio_articulo.stock_usado');
                    // Obtener el stock total fijo del artículo
                    $stockTotal = DB::table('articulos')->where('id', $articuloId)->value('stock');

                    // Obtener la cantidad de stock usado por el servicio que deseas agregar
                    $cantidadStockUsadoNuevoServicio = $articuloDelServicio->stock_usado;

                    // Calcular la cantidad total que se usaría si se agrega el nuevo servicio
                    $nuevaCantidadTotal = $cantidadTotalUtilizada + $cantidadStockUsadoNuevoServicio;

                    if ($nuevaCantidadTotal > $stockTotal) {
                        // El stock se superaría para al menos uno de los artículos
                        $stockSeSupera = true;
                        break; // Salir del bucle, ya que no es necesario verificar los otros artículos
                    }
                }
            }

            if ($existe) {
                $this->alert('error', 'Este servicio ya está asignado a otro evento en esta fecha.');
            } else if ($stockSeSupera == true) {
                $this->alert('error', 'Todo el stock de un artículo dado de este servicio está en uso en esta fecha.');
            } else {
                $this->listaPacks[] = [
                    'id' => $this->pack_seleccionado,
                    'numero_monitores' => $this->preciosMonitores,
                    'precioFinal' => $this->precioFinalPack,
                    'tiempos' => $this->tiemposPack,
                    'horas_inicio' => $this->horasInicioPack,
                    'horas_finalizacion' => $this->horasFinalizacionPack,
                    'tiempos_montaje' => $this->tiemposMontajePack,
                    'tiempos_desmontaje' => $this->tiemposDesmontajePack,
                    'horas_montaje' => $this->horasMontajePack,
                    'id_monitores' => $this->idMonitoresPack,
                    'sueldos_monitores' => $this->sueldoMonitoresPack,
                    'gastos_gasoil' => $this->gastosGasoilPack,
                    'checks_gasoil' => $this->gastosGasoilPack,
                    'pagos_pendientes' => $this->sueldoMonitoresPack,

                ];
                $this->pack_seleccionado = 0;
                $this->preciosMonitores = [];
                $this->tiemposPack = [];
                $this->horasInicioPack = [];
                $this->horasFinalizacionPack = [];
                $this->tiemposMontajePack = [];
                $this->tiemposDesmontajePack = [];
                $this->horasMontajePack = [];
                $this->precioFinal += $this->precioFinalPack;
                $this->precioFinalPack = 0;
            }
        } else {
            $this->alert('error', '¡Selecciona un pack de servicios!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        }
    }

    public function sumarTiempos($id)
    {
        $totalMinutos = 0;

        foreach ($this->listaPacks[$id]['tiempos'] as $tiempo) {
            $partes = explode(':', $tiempo);
            $horas = $partes[0];
            $minutos = $partes[1];
            $totalMinutos += ($horas * 60) + $minutos;
        }

        $horasResultado = floor($totalMinutos / 60);
        $minutosResultado = $totalMinutos % 60;

        return sprintf('%02d:%02d', $horasResultado, $minutosResultado);
    }
    public function addServicio()
    {
        if ($this->servicio_seleccionado != 0) {
            $servicioId = $this->servicio_seleccionado;
            $existe = Presupuesto::whereHas('servicios', function ($query) use ($servicioId) {
                $query->where('servicios.id', $servicioId);
            })
                ->where('fechaEmision', $this->diaEvento)
                ->exists();
            $fechaEvento = $this->diaEvento; // Fecha en la que deseas verificar el stock
            // Obtener los artículos relacionados con el servicio
            $articulosDelServicio = DB::table('servicio_articulo')
                ->where('servicio_id', $servicioId)
                ->get();

            // Variable para rastrear si el stock se supera
            $stockSeSupera = false;
            // Iterar a través de los artículos del servicio y verificar el stock para cada uno
            foreach ($articulosDelServicio as $articuloDelServicio) {
                $articuloId = $articuloDelServicio->articulo_id;

                // Obtener la cantidad total utilizada de este artículo en la fecha indicada
                $cantidadTotalUtilizada = DB::table('presupuestos')
                    ->join('servicio_presupuesto', 'presupuestos.id', '=', 'servicio_presupuesto.presupuesto_id')
                    ->join('servicios', 'servicio_presupuesto.servicio_id', '=', 'servicios.id')
                    ->join('servicio_articulo', 'servicios.id', '=', 'servicio_articulo.servicio_id')
                    ->where('presupuestos.fechaEmision', $fechaEvento)
                    ->where('servicio_articulo.articulo_id', $articuloId)
                    ->sum('servicio_articulo.stock_usado');
                // Obtener el stock total fijo del artículo
                $stockTotal = DB::table('articulos')->where('id', $articuloId)->value('stock');

                // Obtener la cantidad de stock usado por el servicio que deseas agregar
                $cantidadStockUsadoNuevoServicio = $articuloDelServicio->stock_usado;

                // Calcular la cantidad total que se usaría si se agrega el nuevo servicio
                $nuevaCantidadTotal = $cantidadTotalUtilizada + $cantidadStockUsadoNuevoServicio;

                if ($nuevaCantidadTotal > $stockTotal) {
                    // El stock se superaría para al menos uno de los artículos
                    $stockSeSupera = true;
                    break; // Salir del bucle, ya que no es necesario verificar los otros artículos
                }
            }


            if ($existe) {
                $this->alert('error', 'Este servicio ya está asignado a otro evento en esta fecha.');
            } else if ($stockSeSupera == true) {
                $this->alert('error', 'Todo el stock de un artículo dado de este servicio está en uso en esta fecha.');
            } else {
                for ($i = 0; $i > $this->numero_monitores; $i++) {
                    $this->sueldoMonitores[] = $this->servicios->firstWhere('id', $this->servicio_seleccionado)->get('precioMonitor');
                }
                $this->listaServicios[] = [
                    'id' => $this->servicio_seleccionado,
                    'numero_monitores' => $this->numero_monitores,
                    'precioFinal' => $this->precioFinalServicio,
                    'tiempo' => $this->tiempo,
                    'hora_inicio' => $this->hora_inicio,
                    'hora_finalizacion' => $this->hora_finalizacion,
                    'hora_montaje' => $this->horaMontaje,
                    'tiempo_montaje' => $this->tiempoMontaje,
                    'tiempo_desmontaje' => $this->tiempoDesmontaje,
                    'sueldo_monitores' => $this->sueldoMonitores,
                    'id_monitores' => $this->idMonitores,
                    'gasto_gasoil' => $this->gastosGasoil,
                    'check_gasoil' => $this->gastosGasoil,
                    'pago_pendiente' => $this->sueldoMonitores,
                ];
                $this->servicio_seleccionado = 0;
                $this->numero_monitores = 0;
                $this->tiempo = 0;
                $this->tiempoMontaje = 0;
                $this->tiempoDesmontaje = 0;
                $this->hora_inicio = 0;
                $this->hora_finalizacion = 0;
                $this->horaMontaje = 0;
                $this->precioFinal += $this->precioFinalServicio;
                $this->precioFinalServicio = 0;
            }
        } else {
            $this->alert('error', '¡Selecciona un servicio!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        }
    }

    public function deletePack($indice)
    {
        $this->precioFinal -= $this->listaPacks[$indice]['precioFinal'];
        unset($this->listaPacks[$indice]);
        $this->listaPacks = array_values($this->listaPacks);
    }

    public function deleteServicio($indice)
    {
        $this->precioFinal -= $this->listaServicios[$indice]['precioFinal'];
        unset($this->listaServicios[$indice]);
        $this->listaServicios = array_values($this->listaServicios);
    }

    public function updatedDiaEvento()
    {
        if (!isset($this->diaFinal)) {
            $this->diaFinal = $this->diaEvento;
        }
    }


    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'submitEvento',
            'confirmed',
            'calcularPrecio',
            'verContrato',
            'selectCliente',
            'selectedCompanyItem',
            'alertaGuardar'

        ];
    }

    public function alertaGuardar()
    {
        $this->alert('warning', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submitEvento',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('presupuestos.edit', $this->identificador);
    }

    public function verContrato()
    {
        return redirect()->route('contratos.edit', $this->contrato_id);
    }

    public function setGasoil($servicioIndex, $i)
    {
        if ($this->gasoilDistancia != null) {
            $this->listaServicios[$servicioIndex]['gasto_gasoil'][$i] = round((float)($this->gasoilDistancia * $this->gasoilPrecio), 2);
        }
    }
    public function setGasoilPack($packIndex, $keyPack, $i)
    {
        if ($this->gasoilDistancia != null) {
            $this->listaPacks[$packIndex]['gastos_gasoil'][$keyPack][$i] = round((float)($this->gasoilDistancia * $this->gasoilPrecio), 2);
        }
    }

    public function confirmedImprimir()
    {

        $this->diaMostrar = Carbon::now()->locale('es_ES')->isoFormat('D [de] MMMM [de] Y');
        $presupuesto = Presupuesto::find($this->identificador);
        $cliente = Cliente::where('id', $presupuesto->id_cliente)->first();
        $evento = Evento::where('id', $presupuesto->id_evento)->first();
        $packs = ServicioPack::all();
        foreach ($presupuesto->servicios()->get() as $servicio) {
            $listaServicios[] = ['id' => $servicio->id, 'nombre' => $servicio->nombre, 'numero_monitores' => $servicio->pivot->numero_monitores, 'precio_final' => $servicio->pivot->precio_final, 'tiempo' => $servicio->pivot->tiempo, 'hora_inicio' => $servicio->pivot->hora_inicio, 'hora_finalizacion' => $servicio->pivot->hora_finalizacion, 'existente' => 1];
        }

        foreach ($presupuesto->packs()->get() as $pack) {
            $listaPacks[] = ['id' => $pack->id, 'numero_monitores' => json_decode($pack->pivot->numero_monitores, true), 'precio_final' => $pack->pivot->precio_final, 'existente' => 1];
        }

        $filename = Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        $this->ruta = '/contratos/' . $filename;



        if (count($presupuesto->packs) > 0 && count($presupuesto->servicios) > 0) {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'listaServicios' => $listaServicios, 'listaPacks' => $listaPacks, 'packs' => $packs, 'observaciones' => '',
                'nContrato' => $this->nPresupuesto, 'fechaContrato' => $this->fechaEmision, 'authImagen' => 1, 'authMenores' => 1, 'fechaMostrar' => $this->diaMostrar,
            ];
        } else if (count($presupuesto->packs) > 0 && count($presupuesto->servicios) <= 0) {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'listaPacks' => $listaPacks, 'packs' => $packs, 'observaciones' => '',
                'nContrato' => $this->nPresupuesto, 'fechaContrato' => $this->fechaEmision, 'authImagen' => 1, 'authMenores' => 1, 'fechaMostrar' => $this->diaMostrar,
            ];
        } else if (count($presupuesto->packs) <= 0 && count($presupuesto->servicios) > 0) {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'listaServicios' => $listaServicios, 'packs' => $packs, 'observaciones' => '',
                'nContrato' => $this->nPresupuesto, 'fechaContrato' => $this->fechaEmision, 'authImagen' => 1, 'authMenores' => 1, 'fechaMostrar' => $this->diaMostrar,
            ];
        } else {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'packs' => $packs, 'observaciones' => '',
                'nContrato' => $this->nPresupuesto, 'fechaContrato' => $this->fechaEmision, 'authImagen' => 1, 'authMenores' => 1, 'fechaMostrar' => $this->diaMostrar,
            ];
        }


        $path = public_path('contratos');

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $pdf = Pdf::loadView('livewire.contratos.contract-component', $datos)->setPaper('a4', 'vertical')->save(public_path() . $this->ruta); //
        $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });

        $pdf->output();

        $this->confirmed();

        return response()->streamDownload(
            fn () => print($pdf),
            $filename
        );
    }
}
