<?php

namespace App\Http\Livewire\Contratos;

use App\Models\Evento;
use App\Models\Servicio;
use App\Models\ServicioPack;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\MetodoPago;
use App\Models\ServicioEvento;
use App\Models\CuentaBancaria;
use App\Models\Monitor;
use App\Models\Empresa;
use App\Models\Presupuesto;
use App\Models\Programa;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

use IntlDateFormatter;
use Carbon\Carbon;

class EditComponent extends Component
{

    use LivewireAlert;

    public $empresa;
    public $identificador;
    public $contrato;

    public $presupuestos;
    public $presupuesto;
    public $fechaContrato;
    public $id_presupuesto;

    //contrato Servicios
    public $nContrato;
    public $diaEvento;
    public $observaciones = "";


    //controlador
    public bool $clienteExistente = true;
    public bool $clienteRefresh = true;


    //desplegables
    public $solicitante = null;
    public $oldSolicitante = null;

    //cliente
    public $id_cliente;
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

    public $clienteIsSave = false;


    // Evento
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

    public $eventoIsSave = false;


    //Monitores
    public $monitores;

    // Servicios
    public $evento;
    public $id_evento;
    public $id_monitor;
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

    public $servicioEventoList = [];

    public $servicioIsSave;


    public $servicios;


    public $servicio;

    public $eventoServicios;
    public $programas;
    public $listaServicios;
    public $clientes;



    public $nDias;

    // pricing


    public $total;
    public $precioBase;
    public $entrega;
    public $packs;
    public $metodoPago;
    public $metodoTransferencia;
    public $cliente;
    public $cuentaTransferencia;

    //Descuentos
    public $descuento = 0;
    // public $descTotal = 0;
    public $firma;
    public $totalDiscount;

    //triggers
    public $addDiscount = false;
    public $isTransferencia = false;
    public $addPrograma = false;
    public $addCliente = false;
    public $addServicio = false;
    public $addObservaciones = false;


    public $diaMostrar;

    //Consentimiento
    public $authImagen = 0;
    public $authMenores = 0;
    public $responsableTratamiento;

    protected $listeners = ['refreshComponent' => '$refresh'];



    public function mount()
    {
        $this->servicios = Servicio::all();
        $this->clientes = Cliente::all();
        $this->empresa = Empresa::find("1");
        $this->presupuestos = Presupuesto::all();
        $this->contrato = Contrato::find($this->identificador);

        $this->nContrato = $this->contrato->id;
        $this->dia = $this->contrato->dia;
        $this->firma = $this->contrato->cuentaTransferencia;
        $this->observaciones = $this->contrato->observaciones;
        $this->authImagen = $this->contrato->authImagen;
        $this->authMenores = $this->contrato->authMenores;
        $this->metodoPago = $this->contrato->metodoPago;
        $this->id_presupuesto = $this->contrato->id_presupuesto;
        $this->diaMostrar = Carbon::now()->locale('es_ES')->isoFormat('D [de] MMMM [de] Y');
    }


    public function render()
    {
        if ($this->id_presupuesto != 0) {
            $this->presupuesto = Presupuesto::find($this->id_presupuesto);
            $this->cliente = Cliente::where('id', Presupuesto::find($this->id_presupuesto)->id_cliente)->first();
            $this->evento = Evento::where('id', Presupuesto::find($this->id_presupuesto)->id_evento)->first();
            $this->servicios = $this->presupuesto->servicios()->withPivot('numero_monitores', 'precio_final', 'tiempo', 'hora_inicio', 'hora_finalizacion')->get();
            $this->packs = $this->presupuesto->packs()->withPivot('numero_monitores', 'precio_final')->get();
        }

        return view('livewire.contratos.edit-component', ['presupuesto' => $this->presupuesto, 'cliente' => $this->cliente, 'evento' => $this->evento, 'servicios' => $this->servicios, 'packs' => $this->packs]);
    }

    public function loadContrato()
    {
        $this->contrato = Contrato::find($this->identificador);
        $this->id_presupuesto = $this->contrato->id_presupuesto;
        $this->loadPresupuesto($this->id_presupuesto);
        $this->metodoPago = $this->contrato->metodoPago;
        $this->responsableTratamiento = $this->contrato->responsableTratamiento;
        $this->authImagen = $this->contrato->authImagen;
        $this->authMenores = $this->contrato->authMenores;
    }

    public function loadPresupuesto()
    {
        $this->emit("refresh");
        $this->loadPrice();
    }

    public function loadPrice()
    {
        $this->total = Presupuesto::find($this->id_presupuesto)->precioFinal;
        // dd($this->total);
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
    }

    //nombre completo cliente
    public function nombreCliente(int $id)
    {
        $nombreCompleto =  sprintf("%s %s", $this->clientes->find($id)->nombre,  $this->clientes->find($id)->apellido);
        return $nombreCompleto;
    }


    public function fechaContratoFormat()
    {
        date_default_timezone_set("Europe/Madrid");
        setlocale(LC_TIME, 'es_Es.UTF-8', 'esp');
        $fecha = strtotime($this->fechaContrato);
        $formato = new IntlDateFormatter(
            'es-ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            'Europe/Madrid',
            IntlDateFormatter::GREGORIAN,
            "eeee, LLLL 10, yyyy"
        );
        return $formato->format($fecha);
    }

    //Trigger para crear cliente
    public function crearCliente()
    {
        $this->addCliente = true;
    }

    public function uncheckClient()
    {
        $this->clienteIsSave = false;
    }

    public function uncheckEvent()
    {
        $this->eventoIsSave = false;
    }

    //Trigger para añadir observaciones
    public function allowObs()
    {
        $this->addObservaciones = true;
    }

    //Detecta si es una transferencia o no

    public function isTransferencia()
    {
        $this->isTransferencia = $this->metodoPago == $this->metodoTransferencia;
    }

    //Selecciona un cliente y rellena los campos con los datos del mismo
    public function selectClient($solicitante)
    {
        $cliente = $this->clientes->where("id", $solicitante)->first();
        $this->id_cliente = $solicitante;
        $this->trato = $cliente->trato;
        $this->nombre = $cliente->nombre;
        $this->apellido = $cliente->apellido;
    }


    public function diasServicio()
    {
        if ($this->diaEvento === $this->diaFinal) {
            //$this->dia = $this->diaInicio;
            $this->nDias = 1;
        } else {
            $this->nDias = date_diff($this->diaEvento, $this->diaFinal)->format("%R%a");
        }
    }




    //Saca el nombre de un servicio
    public function servicioNombre($id)
    {
        return $this->servicios->find($id)->nombre;
    }

    //Calcula el importe Base de un servicio a partir del numero de monitores y su costo per monitor
    public function servicioImporteBase($id)
    {
        $servicio = $this->servicios->find($id);
        $importeBase = $servicio->precioBase + ($this->numMonitores * $servicio->precioMonitor);
        $this->importeBase = $importeBase;
        $this->importeServicioDesc = $importeBase;
        $this->applyServiceDiscount();
    }

    //Calcula la hora final del servicio a partir de su hora de inicio y su duracion
    public function horaFinalizacion($servicioEvento)
    {
        $hora = date("H:i:s", strtotime("$servicioEvento[horaInicio] + $servicioEvento[tiempo] hours"));
        // $hora = $servicioEvento->horaInicio->modify("+$servicioEvento->tiempo hours");
        return $hora;
    }

    //Calcula y aplica el descuento de un servicio
    public function applyServiceDiscount()
    {
        $importeBase = $this->importeBase;
        $descuentoPC = abs($this->descuentoServicio) / 100;
        $descuento = $importeBase * $descuentoPC;

        $this->importeServicioDesc = $importeBase - $descuento;
    }

    //Rellena los valores por defecto de un servicio a la hora de añadirlo
    public function setDefaultServicios($id)
    {
        $this->servicioMonitores($id);
        $this->servicioImporteBase($id);
        $this->descuentoServicio = 0;
    }

    //Saca el numero minimo de monitores y lo establece como valor por defecto del campo
    public function servicioMonitores($id)
    {
        $this->minMonitores = $this->servicios->find($id)->minMonitor;
        $this->numMonitores = $this->servicios->find($id)->minMonitor;
    }



    public function nombreMonitor(int $id)
    {
        $nombreCompleto =  sprintf("%s %s", $this->monitores->find($id)->nombre,  $this->monitores->find($id)->apellidos);
        return $nombreCompleto;
    }




    public function error($message)
    {

        $this->alert('error', $message,);
    }



    // Al hacer submit en el formulario
    public function update()
    {
        $firmaRuta = 'firmas/' . Carbon::now()->format('Y-m-d_H-i-s') . '.png';
        Storage::disk('public')->put($firmaRuta, base64_decode(Str::of($this->firma)->after(',')));
        $this->cuentaTransferencia = $firmaRuta;
        // Validación de datos
        $validatedData = $this->validate(
            [
                "id_presupuesto" => "required",
                'metodoPago' => 'required',
                'cuentaTransferencia' => 'required',
                'observaciones' => 'nullable',
                'authImagen' => 'required',
                'authMenores' => 'required',
                'dia' => 'required'
            ],
            // Mensajes de error
            [
                'metodoPago.required' => 'La contraseña es obligatoria.',
                'cuentaTransferencia.required' => 'La cuenta es obligatoria',
            ]
        );




        // Guardar datos validados
        $contrato = Contrato::find($this->identificador);
        $contratoSave = $contrato->update($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 15, $this->identificador));


        // Alertas de guardado exitoso
        if ($contratoSave) {

            if ($this->imprimir == 1) {
                $this->alert('success', '¡Contrato actualizado correctamente!', [
                    'position' => 'center',
                    // 'timer' => 1500,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'confirmedImprimir',
                    'confirmButtonText' => 'Imprimir contrato',
                    // 'timerProgressBar' => true,
                ]);
            } else {
                $this->alert('success', '¡Contrato actualizado correctamente!', [
                    'position' => 'center',
                    // 'timer' => 1500,
                    'toast' => false,
                    'showConfirmButton' => true,
                    'onConfirmed' => 'confirmed',
                    'confirmButtonText' => 'ok',
                    // 'timerProgressBar' => true,
                ]);
            }
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del contrato!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
        // } else {
        //     $this->alert('error', 'Se han insertado ' . count($servicios) . ' servicios', [
        //         // 'showConfirmButton' => true,
        //         //'onConfirmed' => 'confirmed',
        //         //'confirmButtonText' => 'ok',
        //         'timerProgressBar' => true,
        //     ]);
        // }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'submitImprimir',
            'confirmedImprimir',
            'update',
            'confirmed',
            'destroy',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        return redirect()->route('contratos.index');
    }

    public function submitImprimir()
    {
        $this->imprimir = 1;
        $this->update();
    }
    public function destroy()
    {
        $contrato = Contrato::find($this->identificador);

        event(new \App\Events\LogEvent(Auth::user(), 16, $contrato->id));

        $deleteContrato = $contrato->delete();

        // Alertas de guardado exitoso
        if ($deleteContrato) {
            $this->alert('success', 'Contrato eliminado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido actualizar la información del presupuesto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function confirmedImprimir()
    {

        $this->diaMostrar = Carbon::now()->locale('es_ES')->isoFormat('D [de] MMMM [de] Y');
        $presupuesto = Presupuesto::find($this->id_presupuesto);
        $cliente = Cliente::where('id', $presupuesto->id_cliente)->first();
        $evento = Evento::where('id', $presupuesto->id_evento)->first();
        $packs = ServicioPack::all();
        $gestor = User::where('id', $presupuesto->gestor_id)->first();

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
                'evento' => $evento, 'listaServicios' => $listaServicios, 'listaPacks' => $listaPacks, 'packs' => $packs, 'observaciones' => $this->observaciones, 'gestor' => $gestor,
                'nContrato' => $presupuesto->nPresupuesto, 'fechaContrato' => $evento->diaEvento, 'authImagen' => $this->authImagen, 'authMenores' => $this->authMenores, 'fechaMostrar' => $this->diaMostrar,
            ];
        } else if (count($presupuesto->packs) > 0 && count($presupuesto->servicios) <= 0) {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'listaPacks' => $listaPacks, 'packs' => $packs, 'observaciones' => $this->observaciones, 'gestor' => $gestor,
                'nContrato' => $presupuesto->nPresupuesto, 'fechaContrato' => $evento->diaEvento, 'authImagen' => $this->authImagen, 'authMenores' => $this->authMenores, 'fechaMostrar' => $this->diaMostrar,
            ];
        } else if (count($presupuesto->packs) <= 0 && count($presupuesto->servicios) > 0) {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'listaServicios' => $listaServicios, 'packs' => $packs, 'observaciones' => $this->observaciones, 'gestor' => $gestor,
                'nContrato' => $presupuesto->nPresupuesto, 'fechaContrato' => $evento->diaEvento, 'authImagen' => $this->authImagen, 'authMenores' => $this->authMenores, 'fechaMostrar' => $this->diaMostrar,
            ];
        } else {
            $datos =  [
                'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago, 'servicios' => Servicio::all(),
                'evento' => $evento, 'packs' => $packs, 'observaciones' => $this->observaciones, 'gestor' => $gestor,
                'nContrato' => $presupuesto->nPresupuesto, 'fechaContrato' => $evento->diaEvento, 'authImagen' => $this->authImagen, 'authMenores' => $this->authMenores, 'fechaMostrar' => $this->diaMostrar,
            ];
        }


        $path = public_path('contratos');

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $pdf = Pdf::loadView('livewire.contratos.contract-component', $datos)->setPaper('a4', 'vertical')->save(public_path() . $this->ruta)->output(); //

        $this->confirmed();

        return response()->streamDownload(
            fn () => print($pdf),
            $filename
        );
    }
}
