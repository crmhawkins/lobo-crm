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

class CreateComponent extends Component
{

    use LivewireAlert;

    public $empresa;
    public $firma = 0;

    public $imprimir = 0;
    public $presupuestos;
    public $presupuesto;
    public $id_presupuesto;

    //contrato Servicios
    public $nContrato = 1;
    public $diaEvento;
    public $observaciones = "";



    //cliente
    public $cliente;



    // Evento

    public $diaFinal;



    //Monitores
    public $monitores;
    public $packs;


    // Servicios
    public $evento;
    public $id_evento;
    public $dia;

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
    public $adelanto;
    public $metodosPago;
    public $metodoPago = "Efectivo";
    public $metodoTransferencia;
    public $cuentas;
    public $cuentaTransferencia;
    public $diaMostrar;


    //triggers
    public $ruta;


    public $fechaContrato;

    //Consentimiento
    public $authImagen = 0;
    public $authMenores = 0;

    protected $listeners = [
        'confirmed' => 'confirmed',
        'isTransferencia' => 'isTransferencia',
        'loadPrice' => 'loadPrice',
        'submit',
        'submitImprimir',
        'confirmedImprimir'
    ];



    public function mount()
    {
        $this->diaEvento = Carbon::now();
        $this->dia = Carbon::now();
        $this->empresa = Empresa::find("1");
        $this->presupuestos = Presupuesto::where('estado', 'Aceptado')->get();
        $this->paquetes = ServicioPack::all();
        $this->servicios = Servicio::all();
        //Autoincrementa automaticamente el numero del contrato antes de que se cree

        if (Contrato::all()->count() > 0) {
            $this->nContrato = Contrato::max("id") + 1;
        } else {
            $this->nContrato = 1;
        }
        $this->monitores = Monitor::all();
        $this->metodosPago = MetodoPago::all();
        $this->cuentas = CuentaBancaria::all();

        $this->metodoTransferencia = count($this->metodosPago) != 0 ? $this->metodosPago->where("nombre", "Transferencia")->first()->id : '';


        $this->diaMostrar = Carbon::now()->locale('es_ES')->isoFormat('D [de] MMMM [de] Y');


        $this->fechaContrato = now();
    }

    public function render()
    {
        if ($this->id_presupuesto != 0) {
            $this->presupuesto = Presupuesto::find($this->id_presupuesto);
            $this->cliente = Cliente::where('id', Presupuesto::find($this->id_presupuesto)->id_cliente)->first();
            $this->evento = Evento::where('id', Presupuesto::find($this->id_presupuesto)->id_evento)->first();
            $this->servicios = $this->presupuesto->servicios()->withPivot('numero_monitores', 'precio_final', 'tiempo', 'hora_inicio', 'hora_finalizacion')->get();
            $this->packs = $this->presupuesto->packs()->withPivot('numero_monitores', 'precio_final', 'tiempos', 'horas_inicio', 'horas_finalizacion')->get();
        }

        return view('livewire.contratos.create-component', ['presupuesto' => $this->presupuesto, 'cliente' => $this->cliente, 'evento' => $this->evento, 'servicios' => $this->servicios, 'packs' => $this->packs]);
    }

    public function updatingId_presupuesto()
    {
    }

    public function loadPresupuesto()
    {
        $this->emit("refresh");
        $this->loadPrice();
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



    public function loadPrice()
    {
        $this->total = Presupuesto::find($this->id_presupuesto)->precioFinal;
        // dd($this->total);
    }


    //Detecta si es una transferencia o no

    public function isTransferencia()
    {


        $this->isTransferencia = $this->metodoPago == $this->metodoTransferencia;
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




    public function nombreMonitor(int $id)
    {
        $nombreCompleto =  sprintf("%s %s", $this->monitores->find($id)->nombre,  $this->monitores->find($id)->apellidos);
        return $nombreCompleto;
    }




    public function error($message)
    {
        $this->alert('error', $message,);
    }

    public function submitImprimir()
    {
        $this->imprimir = 1;
        $this->submit();
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        if ($this->firma != 0) {
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
            $contratoSave = Contrato::create($validatedData);

            event(new \App\Events\LogEvent(Auth::user(), 14, $contratoSave->id));


            // Alertas de guardado exitoso
            if ($contratoSave) {
                if ($this->imprimir == 1) {
                    $this->alert('success', '¡Contrato registrado correctamente!', [
                        'position' => 'center',
                        // 'timer' => 1500,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'onConfirmed' => 'confirmedImprimir',
                        'confirmButtonText' => 'Imprimir contrato',
                        // 'timerProgressBar' => true,
                    ]);
                } else {
                    $this->alert('success', '¡Contrato registrado correctamente!', [
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
                $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
            }
        } else {
            $this->alert('error', '¡No se ha firmado el contrato!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // // Función para cuando se llama a la alerta
    // public function getListeners()
    // {
    //     return [
    //         'confirmed',
    //     ];
    // }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        return redirect()->route('contratos.index');
    }

    public function confirmedImprimir()
    {
        $presupuesto = Presupuesto::find($this->id_presupuesto);
        $cliente = Cliente::where('id', $presupuesto->id_cliente)->first();
        $evento = Evento::where('id', $presupuesto->id_evento)->first();
        $packs = ServicioPack::all();
        $this->servicios = $presupuesto->servicios()->withPivot('numero_monitores', 'precio_final')->get();
        $this->packs = $presupuesto->packs()->withPivot('numero_monitores', 'precio_final')->get();
        $filename = Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        $this->ruta = '/contratos/' . $filename;

       


        $datos =  [
            'presupuesto' => $presupuesto, 'cliente' => $cliente, 'metodoPago' => $this->metodoPago,
            'evento' => $evento, 'listaServicios' => $this->servicios, 'listaPacks' => $this->packs, 'packs' => $packs, 'observaciones' => $this->observaciones,
            'nContrato' => $this->nContrato, 'fechaContrato' => $this->fechaContrato, 'authImagen' => $this->authImagen, 'authMenores' => $this->authMenores, 'fechaMostrar' => $this->diaMostrar, 'firma' => $this->cuentaTransferencia
        ];

        $path = public_path('contratos');

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }

        $pdf = Pdf::loadView('livewire.contratos.contract-component', $datos)->setPaper('a4', 'vertical')->save(public_path() . $this->ruta); 
        $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });
        
        $this->confirmed();
        
        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename
        );
    }
}
