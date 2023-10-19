<?php

namespace App\Http\Livewire\ResumenDias;

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

class ShowComponent extends Component
{
    use LivewireAlert;
    // public $search;
    public $dia;
    public $programasDia;
    public $clientes;
    public $eventos;
    public $monitores;

    public $selectedEventos = [];

    public $programasEvento;
    public $servicioPrograma;
    public $resumenDia = [];
    public $serviciosEvento;
    public $serviciosPrograma;

    protected $listeners = [
        'loadDay' => 'loadDay',
    ];


    public function mount()
    {


        // dd($this->resumenDia);
        //    dd($this->programasDia);
    }

    public function loadDay()
    {
        // dd($this->dia);
        $this->resumenDia = [];
        $date = Carbon::parse($this->dia);
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
            $this->resumenDia[$i] = $evento;
            
        }
        // dd($this->resumenDia);
    }

    public function getTotalMonitores($indice)
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        $servicios =  $this->resumenDia[$indice]["servicios"];

        foreach ($servicios as $servicio) {
            foreach ($servicio["programas"] as $programa) {
                $total += $programa["precioMonitor"];
            }
        }


        return $total;
    }
    public function getTotalMonitoresPagado($indice)
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        $servicios =  $this->resumenDia[$indice]["servicios"];

        foreach ($servicios as $servicio) {
            foreach ($servicio["programas"] as $programa) {
                $total += $programa["pagado"];
            }
        }


        return $total;
    }
    public function getTotalMonitoresDesplazamiento($indice)
    {
        $total = 0;
        // dd($this->resumenDia[$indice]["servicios"]);
        $servicios =  $this->resumenDia[$indice]["servicios"];

        foreach ($servicios as $servicio) {
            foreach ($servicio["programas"] as $programa) {
                $total += $programa["costoDesplazamiento"];
            }
        }


        return $total;
    }

    public function getGastos($indice)
    {
        $monitores = $this->getTotalMonitores($indice);
        $desplazamiento = $this->getTotalMonitoresDesplazamiento($indice);

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

        return view('livewire.resumen-dias.show-component');
    }
}
