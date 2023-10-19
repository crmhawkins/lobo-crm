<?php

namespace App\Http\Livewire\ResumenMensual;

use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Programa;
use App\Models\Evento;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\GastosMensuales;
use App\Models\Monitor;
use App\Models\Presupuesto;
use App\Models\Servicio;
use App\Models\ServicioEvento;
use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EditComponent extends Component
{
    use LivewireAlert;
    // public $search;

    public $identificador;
    public $day;
    public $month;
    public $gastosMes;

    public $semanas = [];
    public $gastosSemanal = [];
    public $nWeeks;
    public $currentWeek;

    public $programasDia;
    public $clientes;
    public $eventos;
    public $monitores;
    public $i_semana;

    public $gastosMesArray = [];

    protected $listeners = [
        'loadWeek' => 'loadWeek',
        'loadMonth' => 'loadMonth',
        'refreshComponent' => '$refresh',
    ];


    public function mount()
    {
        $gastosMes = GastosMensuales::where("id", $this->identificador)->first();
        $day = Carbon::parse($gastosMes->mes);
        $this->day = $day->toDateString();
        $this->month = substr_replace($this->day, "", strlen($this->day) - 3, strlen($this->day));
        $this->loadMonth();
        $this->loadWeek();
        //    dd($this->programasDia);
    }

    public function loadMonth()
    {
        $day = Carbon::parse($this->day);

        $mes = GastosMensuales::where('id', $this->identificador)->first();

        if ($mes == null) {
            $defaultMonth = GastosMensuales::where('id', 1)->first();
            $lastId = GastosMensuales::max('id');

            $month = $defaultMonth->replicate();
            $month->id = $lastId + 1;
            $month->mes = $this->day;
            $month->save();
            $this->gastosMes = $month;
        } else {
            $this->gastosMes = $mes;
            $this->gastosMesArray = $mes->toArray();
        }

        $daysMonth = $day->daysInMonth;
        $strDay = $this->day;
        $endDayDateStr = substr_replace($strDay, strval($daysMonth), strlen($strDay) - 2, strlen($strDay));
        $firstDayDateStr = substr_replace($strDay, "01", strlen($strDay) - 2, strlen($strDay));
        $endDayDate = Carbon::parse($endDayDateStr);
        $firstDayDate = Carbon::parse($firstDayDateStr);


        $period = CarbonPeriod::create($firstDayDate, $endDayDate);
        $semana = [];
        foreach ($period as $i => $date) {

            $semana[$date->weekNumberInMonth][$date->format("Y-m-d")] = $date;
        }
        $this->semanas = $semana;
    }


    public function aplicarCambios()
    {
        unset($this->gastosMesArray["id"]);
        unset($this->gastosMesArray["updated_at"]);
        unset($this->gastosMesArray["created_at"]);
        foreach ($this->gastosMesArray as $key => $gasto) {

            if ($key !== "mes") {
                // dd($key);
                if ($gasto === null || $gasto === "" || $gasto < 0) {
                    $this->gastosMesArray[$key] = 0;
                }
            }
        }
        $this->gastosMes = GastosMensuales::where("id", $this->identificador)->first();
        $this->gastosMes->update($this->gastosMesArray);
        $this->loadMonth();
        $this->loadWeek();

        $this->emit('refreshComponent');

        // dd($this->nWeeks);
    }

    public function loadWeek()
    {
        $nWeeks = count($this->semanas);
        $gastos = $this->gastosMes;

        $this->nWeeks = $nWeeks;
        $this->currentWeek = Carbon::now()->weekNumberInMonth;

        $gastosSemana =  [
            'personalCentralYSegSoc' => $gastos->personalCentralYSegSoc / $nWeeks,
            'alarma' => $gastos->alarma / $nWeeks,
            'seguro' => $gastos->seguro / $nWeeks,
            'telefonia' => $gastos->telefonia / $nWeeks,
            'gestoriaFiscal' => $gastos->gestoriaFiscal / $nWeeks,
            'gestoriaLaboral' => $gastos->gestoriaLaboral / $nWeeks,
            'alquileres' => $gastos->alquiler / $nWeeks,
            'bancos' => $gastos->bancos / $nWeeks,
            'aberasConsultores' => $gastos->aberasConsultores / $nWeeks,
            'informatico' => $gastos->informatico / $nWeeks,
            'comunidad' => $gastos->comunidad / $nWeeks,
            'suministros' => $gastos->suministros / $nWeeks,
            'digmep' => $gastos->digmep / $nWeeks,
            'carlos' => $gastos->carlos / $nWeeks,
            'mybox' => $gastos->mybox / $nWeeks
        ];

        // dd($gastosSemana);

        $this->gastosSemanal = $gastosSemana;
        // dd($semana);
    }

    public function back(){
        return redirect()->route('resumen-mensual.index');
    }

    // public function loadWeek()
    // {
    //     $day = Carbon::parse($this->day);
    //     $weekStartDate = $day->startOfWeek()->format('Y-m-d H:i');
    //     $weekEndDate = $day->endOfWeek()->format('Y-m-d H:i');
    //     $period = CarbonPeriod::create($weekStartDate, $weekEndDate);
    //     $semana = [];
    //     foreach ($period as $i => $date) {

    //         $semana[$i] = $date->format("Y-m-d");
    //     }

    //     $this->semanas[count($this->semanas)] = $semana;
    //     // dd($semana);
    // }

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

        return view('livewire.resumen-mensual.edit-component');
    }
}
