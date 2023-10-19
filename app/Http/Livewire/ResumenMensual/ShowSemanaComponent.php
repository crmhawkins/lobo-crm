<?php

namespace App\Http\Livewire\ResumenMensual;

use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Programa;
use App\Models\Evento;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Monitor;
use App\Models\Presupuesto;
use App\Models\Servicio;
use App\Models\ServicioEvento;
use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class ShowSemanaComponent extends Component
{
    use LivewireAlert;
    // public $search;
    public $day;
    public $index;
    public $programasDia;
    public $clientes;
    public $eventos;
    public $monitores;

    public $selectedEventos = [];

    public $programasEvento;
    public $servicioPrograma;
    public $resumenDia = [];
    public $semana = [];
    public $serviciosEvento;
    public $serviciosPrograma;


    //Semana
    public $gastos;
    public $gastosSemana;
    public $rentabilidad = 0;
    public $balance = 0;

    //Eventos
    public $ingresosBrutos;
    public $liquido;
    public $adelantoEventos;
    public $pteCobrar;

    //Monitores
    public $salarioMonitores;
    public $pagadoMonitores;
    public $pteMonitores;
    public $desplazamiento;

    //todo
    public $segurosSociales;
    public $materialFungible;
    public $compras;
    public $consumosEventos;
    public $alquilerFurgonetas;

    protected $listeners = [
        'loadDay' => 'loadDay',
        'refreshComponent' => '$refresh',
    ];


    public function mount()
    {
        
        $this->loadWeek();
        $this->salarioMonitores = $this->getTotalMonitores();
        $this->pagadoMonitores = $this->getTotalMonitoresPagado();
        $this->pteMonitores = $this->getPteMonitores();
        $this->desplazamiento = $this->getTotalMonitoresDesplazamiento();
        $this->gastos = $this->getGastos();
        $this->adelantoEventos = $this->getAdelantoEventos();
        $this->ingresosBrutos = $this->getPrecioEventos();
        $this->pteCobrar = $this->getPendienteCobrarEvento();
        $this->liquido = $this->ingresosBrutos - $this->adelantoEventos;

        if($this->index > 2){
            // dd($this->resumenDia);
        }
    }

    public function loadWeek()
    {
        // dd($this->day);
        $day = Carbon::parse($this->day);
       
        $weekStartDate = $day->startOfWeek()->format('Y-m-d H:i');
        // dd($weekStartDate);

        $weekEndDate = $day->endOfWeek()->format('Y-m-d H:i');
        $period = CarbonPeriod::create($weekStartDate, $weekEndDate);
        $semana = [];
        foreach ($period as $i => $date) {

            $semana[$i] = $date->format("Y-m-d");
        }

        $this->semana = $semana;
        foreach ($semana as $day) {
            $this->loadDay($day);
        }
        // dd($this->semana);
        
    }
    public function loadDay($day)
    {
        // dd($this->dia);
        $resumenDia = [];
        // dd($day);
        $date = Carbon::parse($day);
        // $this->eventos = Evento::whereRaw("diaInicio <= '{$this->dia}' AND diaFinal >= '{$this->dia}' ")->get();
        // $eventos_id = Evento::whereRaw("diaInicio <= '{$this->dia}' AND diaFinal >= '{$this->dia}' ")->get('id');
        $this->serviciosPrograma = ServicioEvento::whereDate("dia", '=', $date)->pluck("id");
        $this->serviciosEvento = ServicioEvento::whereDate("dia", '=', $date)->pluck("id_evento");
        $this->programasDia = Programa::whereIn("id_servicioEvento", $this->serviciosPrograma)->get();
        $eventos = Evento::whereIn("id", $this->serviciosEvento)->get()->toArray();

        foreach ($eventos as $i => $evento) {
            $serviciosEvento = ServicioEvento::with("servicio")->where("id_evento", $evento["id"])->whereDate("dia", '=', $date)->get()->toArray();

            $presupuesto = Presupuesto::where("id_evento", $evento["id"])->first()->toArray();
            $evento["presupuesto"] = $presupuesto;
            $contrato = Contrato::where("id_presupuesto", $presupuesto["id"])->first();

            if ($contrato !== null) {
                $evento["presupuesto"]["contrato"] = $contrato->toArray();
            }
            $evento["servicios"] = [];

            foreach ($serviciosEvento as $index => $serv) {
                $serv["programas"] = Programa::where("id_servicioEvento", $serv["id"])->with("monitor")->get()->toArray();
                $evento["servicios"][$index] = $serv;
            }
            // dd($evento);
            $resumenDia[$i] = $evento;
        }
        $this->resumenDia[$day] = $resumenDia;
        // dd($day);
        // if($day === "2023-06-17"){
        //     dd($resumenDia);
        // }
        // dd($this->resumenDia);
    }

    public function getTotalMonitores()
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        foreach ($this->resumenDia as $dia) {
            foreach ($dia as $servicioEvento) {
                foreach ($servicioEvento["servicios"] as $servicio) {
                    foreach ($servicio["programas"] as $programa) {
                        $total += $programa["precioMonitor"];
                    }
                }
            }
        }


        return $total;
    }

    public function getTotalMonitoresPagado()
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);

        foreach ($this->resumenDia as $dia) {
            foreach ($dia as $servicioEvento) {
                foreach ($servicioEvento["servicios"] as $servicio) {
                    foreach ($servicio["programas"] as $programa) {
                        $total += $programa["pagado"];
                    }
                }
            }
        }


        return $total;
    }

    public function getPteMonitores(){
        $total = $this->salarioMonitores - $this->pagadoMonitores;
        return $total;
    }

    public function getTotalMonitoresDesplazamiento()
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        foreach ($this->resumenDia as $dia) {
            foreach ($dia as $servicioEvento) {
                foreach ($servicioEvento["servicios"] as $servicio) {
                    foreach ($servicio["programas"] as $programa) {
                        $total += $programa["pagado"];
                    }
                }
            }
        }


        return $total;
    }

    public function getAdelantoEventos()
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        foreach ($this->resumenDia as $dia) {
            foreach ($dia as $servicioEvento) {
                $total += $servicioEvento["presupuesto"]["adelanto"];
            }
        }


        return $total;
    }
    public function getPrecioEventos()
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        foreach ($this->resumenDia as $dia) {
            foreach ($dia as $servicioEvento) {
                $total += $servicioEvento["presupuesto"]["precioFinal"];
            }
        }


        return $total;
    }

    public function getPendienteCobrarEvento(){
        $pendiente = $this->ingresosBrutos - $this->adelantoEventos;
        return $pendiente;
    }

    public function getGastos()
    {
        $monitores = $this->salarioMonitores;
        $desplazamiento = $this->desplazamiento;

        // Todo gastos seguridad social y material

        $total = $monitores + $desplazamiento;
        return $total;
    }

    public function getBalance($indice)
    {
        $gastos = $this->getGastos($indice);
        $ingresos = $this->resumenDia[$indice]["presupuesto"]["precioFinal"];
        $total = $ingresos - $gastos;

        return $total;
    }

    public function render()
    {

        return view('livewire.resumen-mensual.show-semana-component');
    }
}
