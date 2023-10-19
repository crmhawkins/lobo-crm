<?php

namespace App\Http\Livewire\Contratos;

use App\Models\Cliente;
use App\Models\Evento;
use App\Models\ServicioEvento;
use App\Models\Monitor;
use App\Models\Contrato;
use App\Models\Programa;
use App\Models\Servicio;
use App\Models\ServicioPack;
use Illuminate\Support\Arr;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ContractComponent extends Component
{

    use LivewireAlert;

    public $identificador;

    public $ncontrato;
    public $contrato;
    public $fechaEmision;
    public $id_evento = 0; // 0 por defecto por si no se selecciona ninguna
    public $id_cliente = 0; // 0 por defecto por si no se selecciona ninguna
    public $observaciones = "";

    //cliente
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

    public $clienteIsSave = true;

    public $clientes;

    public $cliente;

    public $clienteExistente = true;

    //Evento
    public $evento;

    public $eventoNombre;
    public $eventoProtagonista;
    public $eventoNiños;
    public $eventoContacto;
    public $eventoParentesco;
    public $eventoTelefono;
    public $eventoLugar;
    public $eventoLocalidad;
    public $eventoMontaje = false;
    public $diaFinal;
    public $diaEvento;
    public $dias = [];

    public $eventoIsSave = true;


    //monitores
    public $monitores;
    public $id_monitor;
    public $desplazamiento;

    // Servicios
    public $id_servicio;
    public $comienzoMontaje;
    public $tiempo;
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


    public $servicio;

    public $packs;
    public $pack;

    public $eventoServicios;
    public $programas;
    public $listaServicios;

    //pricing
    public $precioBase;
    public $descuento = 0;
    public $precioFinal;
    public $entrega;
    public $adelanto;


    //Descuentos
    public $descTotal = 0;
    public $entregaDiscount;
    public $totalDiscount;


    //triggers
    public $addDiscount = false;
    public $addPrograma = false;
    public $addCliente = true;
    public $addServicio = false;
    public $addObservaciones = false;


    //Debug
    public $baseMonitor;
    public $servicioEvento;
    public $key;
    public $currentProgram;
    public $validatePrograms;
    public $type;
    public $lowerN;
    public $empty;
    public $timeDif;
    public $period;

    public $listeners = [
        'confirmed' => 'confirmed',
        'calcularPrecio' => 'calcularPrecio',
        'refreshComponent' => '$refresh'
    ];

    // public $metodoPago;


    public function mount()
    {

        $this->contrato = contrato::find($this->identificador);
        $this->ncontrato = $this->contrato->id;

        $this->observaciones = $this->contrato->observaciones;
       

        $this->addObservaciones = $this->observaciones > " ";
        $this->precioBase = $this->contrato->precioBase;
        $this->precioFinal = $this->contrato->precioFinal;
        $this->descuento = $this->contrato->descuento;
        $this->addDiscount = $this->descuento > 0;
        $this->adelanto = $this->contrato->adelanto;

        //Cliente
        $this->id_cliente = $this->contrato->id_cliente;
        $this->clientes = Cliente::all(); // datos que se envian al select2

        $this->setupClient($this->id_cliente);


        //Evento
        $this->id_evento = $this->contrato->id_evento;
        $this->setupEvento($this->id_evento);

        //Servicio Evento


        foreach ($this->dias as $dia) {
            $this->serviciosListDia[$dia] = ServicioEvento::where("id_evento", $this->evento->id)->whereDate("dia",  $dia)->get()->toArray();
        }
        // $this->servicioEventoList = ServicioEvento::where("id_evento", $this->evento->id)->get()->toArray();

        $this->programas = Programa::all();

        $this->packs = ServicioPack::all();


        $this->monitores = Monitor::all();
        $this->servicios = Servicio::all();


        $this->fechaEmision = $this->contrato->fechaEmision;
        $this->setupServicioEventoList($this->serviciosListDia);
        $this->getTotalPrice();
    }

    public function render()
    {
        return view('livewire.contratos.contract-component');
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
        $this->dia = $this->diaEvento;
        $this->getDias();
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

    public function setupClient($id)
    {

        $this->cliente = $this->clientes->find($id);
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

            if($servicioEventoList[0]["id_servicio"] === 0 && $index == 0){
                $servicioEventoList[0] = $serv;
            }else{
                array_push($servicioEventoList, $serv);
            }
            
        }
        $this->serviciosListDia[$this->dia] = $servicioEventoList;
        // dd($servicioEventoList);

        foreach ($servicioEventoList as $index => $servicio) {
            // dd($servicio);
            $this->addProgramServiceField($servicio["numMonitores"], $index);
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
                $servicioEvento["horaFin"] = $this->horaFinalizacion($servicioEvento);


                $servicioEventoList[$key] =  $servicioEvento;
                $this->serviciosListDia[$dia] = $servicioEventoList;
                $this->refreshEndTime($key, $dia);
                $this->addProgramServiceField($servicioEvento["numMonitores"], $key, $dia);
            }
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
            $this->alert('error', '¡No se ha podido actualizar la información del usuario!', [
                'position' => 'center',
                'timer' => 3000,
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
                'diaFinal' => 'required',

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

        $this->eventoIsSave = Evento::where("id", $this->id_evento)->update($eventValidate);

        if ($this->eventoIsSave) {
            $this->id_evento = $this->eventoIsSave;


            $this->alert('success', 'Evento actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido actualizar la información del evento!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function setUpServiceForm($i)
    {
        $dia = $this->dias[$i];
        if (empty($this->serviciosListDia[$dia])) {
            $this->addServiceField($dia);
        }
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


    // public function servicioMonitores($key, $id, $dia)
    // {
    //     $this->servicioEvento = $this->servicioEventoList[$key];
    //     $minMonitores = $this->servicios->find($id)->minMonitor;
    //     $this->minMonitores = $this->servicios->find($id)->minMonitor;

    //     $servicioEvento["numMonitores"] = $this->servicios->find($id)->minMonitor;

    //     $this->numMonitores = $servicioEvento["numMonitores"];

    //     return $minMonitores;
    // }

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

    //Comprueba el min de monitores y atualiza la lista del formulario
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

    public function refreshEndTime($key, $dia)
    {
        $serviciosDia = $this->serviciosListDia[$dia];
        $servicioEvento = $serviciosDia[$key];
        $horaInicio = $servicioEvento["horaInicio"];
        $tiempo  = $servicioEvento["tiempo"];


        $servicioEvento["horaFin"] = $this->horaFinalizacion($servicioEvento);

        $serviciosDia[$key] = $servicioEvento;

        $this->serviciosListDia[$dia] = $serviciosDia;


        $comienzoMontaje = $servicioEvento["comienzoMontaje"];
        $tiempo  = $servicioEvento["tiempoMontaje"];
        $minMontaje = strtotime("$comienzoMontaje + $tiempo minutes");

        if (strtotime($horaInicio) < $minMontaje) {
            $this->refreshMountTime($key, $dia);
        }
    }

    // public function setInicioPrograma($keyServicio, $keyPrograma)
    // {
    //     $servicioEvento = $this->servicioEventoList[$keyServicio];
    //     $programa = $servicioEvento["programas"][$keyPrograma];


    //     $programa["comienzoMontaje"] = $servicioEvento["comienzoMontaje"];


    //     $servicioEvento["programas"][$keyPrograma] = $programa;

    //     $this->servicioEventoList[$keyServicio] = $servicioEvento;
    // }


    public function refreshMountTime($key)
    {
        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$key];
        $horaInicio = $servicioEvento["horaInicio"];
        $tiempo  = $servicioEvento["tiempoMontaje"];
        $horaMontaje = $servicioEvento["comienzoMontaje"];

        $this->timeDif = strtotime("$horaInicio - $tiempo minutes");
        if (strtotime("$horaInicio - $tiempo minutes") < strtotime($horaMontaje)) {
            $horaMontaje  = date("H:i", strtotime("$horaInicio - $tiempo minutes"));

            $servicioEvento["comienzoMontaje"] = $horaMontaje;

            $serviciosDia[$key] = $servicioEvento;
            $this->serviciosListDia[$this->dia]  = $serviciosDia;
        }
    }





    //Saca el nombre de un servicio
    public function servicioNombre($id)
    {
        return $this->servicios->find($id)->nombre;
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
            "tiempoDesmontaje" => 0,
        );

        $this->serviciosListDia[$this->dia] = $servicioEventoList;

        // $this->programas[count($this->servicioEventoList)] = [];
    }


    public function addProgramServiceField($numMonitores, $index)
    {

        $serviciosDia = $this->serviciosListDia[$this->dia];
        $servicioEvento = $serviciosDia[$index];

        // dd($serviciosDia);
        $id = $servicioEvento["id_servicio"];

        if ($id > 0) {
            $precioMonitor = $this->servicios->find($id)->precioMonitor;

            $programas = [];

            $this->empty = empty($servicioEvento["programas"]);
            $i = 0;
            if (!empty($servicioEvento["programas"])) {
                $i = count($servicioEvento["programas"]);
            }

            $programas = $servicioEvento["programas"];

            for ($i; $i < $numMonitores; $i++) {

                $programas[$i] = array(
                    "id_monitor" => 0,
                    "id_servicioEvento" => 0,
                    "dia" => $this->dia,
                    "precioMonitor" => $precioMonitor,
                    "costoDesplazamiento" => 0,
                    "comienzoMontaje" => $servicioEvento["comienzoMontaje"],
                    "comienzoEvento" => $servicioEvento["horaInicio"],
                    "horas" => $servicioEvento["tiempo"],
                    "tiempoDesmontaje" => 0,
                    "horaFin" => $this->horaFinalizacion($servicioEvento),
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
            $this->getTotalPrice();
        }
        // dd($this->serviciosListDia);
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


    public function removeServicio($key, $dia)
    {

        //Miro si existe en la bd y si es asi lo borro

        if (isset($this->serviciosListDia[$dia][$key]["id"])) {
            ServicioEvento::find($this->serviciosListDia[$dia][$key]["id"])->delete();
        }

        //Lo quito de la lista de servicios
        unset($this->serviciosListDia[$dia][$key]);


        $this->getTotalPrice();
    }


    //Calcula el precio total teniendo en cuenta el precio de la lista de los servicios y aplica un descuento si es necesario
    public function getTotalPrice()
    {
        $total = 0;
        if($this->adelanto < 0 || $this->adelanto === null || $this->adelanto === ""){
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
        if ($this->contrato->descuento > 0 && $this->addDiscount) {
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

    public function checkClient()
    {
        $this->clienteIsSave = true;
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
            // dd($servicioEventoList);
            foreach ($servicioEventoList as $index => $servicio) {


                // $programas[$i + $index] = $servicio["programas"];
                $exist = isset($servicio["id"]);
                $programas = $servicio["programas"];
               
                unset($servicio["programas"]);
                unset($servicio["horaFin"]);
                $exist = isset($servicio["id"]);
                if ($exist) {
                    ServicioEvento::find($servicio["id"])->update($servicio);
                    $serv = ServicioEvento::find($servicio["id"])->toArray();
                    // dd($serv);
                } else {
                    $serv = ServicioEvento::create($servicio)->toArray();
                   
                }

                if ($serv) {
                    foreach ($programas as $programa) {
                        $programa["id_servicioEvento"] = $serv["id"];
                        if (array_key_exists("id", $programa)) {
                            Programa::find($programa["id"])->update($programa);
                        } else {
                            Programa::create($programa);
                        }
                    }
                    $servicios[$index] = $serv;
                    // dd($programa);
                    $programa[$index]["id_servicioEvento"] = $serv["id"];
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

public function refresh(){

}

    //
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
    public function update()
    {
        $servicios = $this->submitServicioList();

        if ($servicios) {



            // Validación de datos
            $validatedData = $this->validate(
                [
                    'fechaEmision' => 'required',
                    'id_evento' => 'required',
                    'id_cliente' => 'required',
                    'precioBase' => 'required',
                    'precioFinal' => '',
                    'descuento' => '',
                    'adelanto' => '',
                    'observaciones' => '',

                ],
                // Mensajes de error
                [
                    'fechaEmision' => 'El número de contrato es obligatorio.',
                    'id_evento.required' => 'La fecha de emision es obligatoria.',
                    'id_cliente.required' => 'El alumno es obligatorio.',
                    'precioBase.required' => 'El curso es obligatorio.',
                    'precioFinal.required' => 'Los detalles son obligatorios',
                    // 'total_sin_iva.required' => '',
                    // 'iva.required' => '',
                    // 'descuento.required' => '',
                    // 'descuento.required' => 'El precio es obligaorio',
                    'estado.required' => 'El estado es obligatorio',
                    // 'observaciones.required' => 'La observación es obligatoria',
                ]
            );



            // Guardar datos validados
            $presupuesosSave = contrato::where("id", $this->ncontrato)->update($validatedData);

            // Alertas de guardado exitoso
            if ($presupuesosSave) {
                $this->alert('success', '¡contrato actualizado correctamente!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'confirmed',
                    'confirmButtonText' => 'ok',
                    'timerProgressBar' => true,
                ]);
                $this->emit("loadPrice", $this->precioFinal);
            } else {
                $this->alert('error', '¡No se ha podido actualizar la información del contrato!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
            }
        }
    }

    // Función para cuando se llama a la alerta
    // public function getListeners()
    // {
    //     return [
    //         'confirmed' => 'confirmed',
    //         'calcularPrecio' => 'calcularPrecio',
    //         'refreshComponent' => '$refresh'
    //     ];
    // }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        // return redirect()->route('contratos.index');
    }
}
