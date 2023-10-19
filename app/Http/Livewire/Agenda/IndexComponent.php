<?php

namespace App\Http\Livewire\Agenda;

use App\Models\TipoEvento;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Programa;
use App\Models\Evento;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Monitor;
use App\Models\PackPresupuesto;
use App\Models\Presupuesto;
use App\Models\Servicio;
use App\Models\ServicioPresupuesto;
use App\Models\ServicioPack;
use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class IndexComponent extends Component
{

    use LivewireAlert;
    // public $search;
    public $dias = [];
    public $semana;
    public $categorias;
    public $clientes;
    public $servicios_presupuesto;
    public $packs_presupuesto;
    public $datos_servicio = [];
    public $datos_pack = [];
    public $fechas = [];
    public $listaPacks = [];
    public $listaServicios = [];
    public $packs;
    public $monitores;
    public $servicios;
    public $presupuestos;
    public $eventos;

    protected $listeners = [
        'loadWeek' => 'loadWeek',
        'loadMonth' => 'loadMonth',
        'refreshComponent' => '$refresh',
        'confirmed' => 'recargarPagina'
    ];


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->categorias = TipoEvento::all();
        $this->clientes = Cliente::all();
        $this->packs = ServicioPack::all();
        $this->servicios = Servicio::all();
        $this->monitores = Monitor::all();
        $this->servicios_presupuesto = ServicioPresupuesto::all();
        $this->packs_presupuesto = PackPresupuesto::all();
        $this->semana = Carbon::now()->year . "-W" . Carbon::now()->weekOfYear;
        $this->cambioSemana();
    }


    public function render()
    {
        return view('livewire.agenda.index-component');
    }
    public function cambioSemana()
    {
        $this->fechas = [];
        $this->dias = [];
        $this->listaPacks = [];
        $this->listaServicios = [];

        list($year, $week) = explode('-W', $this->semana);

        $fechaInicio = Carbon::now()->setISODate($year, $week, 1); // El 1 al final establece el día de inicio de la semana a lunes
        for ($i = 0; $i < 7; $i++) {
            $this->fechas[] = $fechaInicio->copy()->addDays($i)->toDateString();
        }
        $this->eventos = Evento::whereBetween('diaEvento', [$this->fechas[0], $this->fechas[6]])->get();

        foreach ($this->fechas as $diaIndex => $dia) {
            $date = Carbon::parse($dia);
            $formattedDate = $date->isoFormat('dddd, D [de] MMMM [de] Y');
            $this->dias[$diaIndex] = str_replace('s畸bado', 'sábado', $formattedDate);  // lunes, 21 de agosto de 2023
        }

        foreach ($this->eventos as $evento) {
            if ($this->presupuestos->where('id_evento', $evento->id)->count() > 0) {
                $presupuesto = $this->presupuestos->where('id_evento', $evento->id)->first();
                if ($presupuesto->servicios()->count() > 0) {
                    foreach ($this->servicios_presupuesto->where('presupuesto_id', $presupuesto->id) as $servicioIndex => $itemServicio) {
                        $this->getMonitoresServicio($presupuesto->id, $itemServicio->servicio_id);
                        $this->getPrecioMonitoresServicio($presupuesto->id, $itemServicio->servicio_id);
                        $this->getPagosPendientesServicio($presupuesto->id, $itemServicio->servicio_id);
                    }
                }
                if ($presupuesto->packs()->count() > 0) {
                    foreach ($this->packs_presupuesto->where('presupuesto_id', $presupuesto->id) as $packIndex => $itemPack) {
                        $this->getMonitoresPack($presupuesto->id, $itemPack->pack_id);
                        $this->getPrecioMonitoresPack($presupuesto->id, $itemPack->pack_id);
                        $this->getPagosPendientesPack($presupuesto->id, $itemPack->pack_id);
                    }
                }
            }
        }
        $this->emit('refreshComponent');
    }

    public function getNombreServicio($id)
    {
        if($this->servicios->find($id) == null){
            return "";
        }else{
            return $this->servicios->find($id)->nombre;
        }
    }

    public function getNombrePack($id)
    {
        return $this->packs->find($id)->nombre;
    }

    public function getPrecioMonitoresServicio($id1, $id2)
    {
        $pivote = ServicioPresupuesto::where('presupuesto_id', $id1)->where('servicio_id', $id2)->first();
        if ($pivote != null) {
            if (empty(json_decode($pivote->sueldo_monitores))) {
                $precio = Servicio::where('id', $id2)->first()->precioMonitor;
                $sueldo = (int) date_parse_from_format('h', $pivote->tiempo) * (int) $precio;
                for ($i = 0; $i < $pivote->numero_monitores; $i++) {
                    $this->datos_servicio[$id1][$id2]['id_monitores'][$i] = $sueldo;
                }
            } else {
                $this->datos_servicio[$id1][$id2]['sueldo_monitores'] = json_decode($pivote->sueldo_monitores);
            }
        }
    }

    public function getMonitoresServicio($id1, $id2)
    {
        $pivote = ServicioPresupuesto::where('presupuesto_id', $id1)->where('servicio_id', $id2)->first();
        if ($pivote != null) {
            if (empty(json_decode($pivote->id_monitores))) {
                for ($i = 0; $i < $pivote->numero_monitores; $i++) {
                    $this->datos_servicio[$id1][$id2]['id_monitores'][$i] = 0;
                }
            } else {
                $this->datos_servicio[$id1][$id2]['id_monitores'] = json_decode($pivote->id_monitores);
            }
        }
    }

    public function getPagosPendientesServicio($id1, $id2)
    {
        $pivote = ServicioPresupuesto::where('presupuesto_id', $id1)->where('servicio_id', $id2)->first();
        if ($pivote != null) {
            if (empty(json_decode($pivote->pago_pendiente))) {
                for ($i = 0; $i < $pivote->numero_monitores; $i++) {
                    $this->datos_servicio[$id1][$id2]['pago_pendiente'][$i] = 0;
                }
            } else {
                $this->datos_servicio[$id1][$id2]['pago_pendiente'] = json_decode($pivote->pago_pendiente);
            }
        }
    }

    public function getPrecioMonitoresPack($id1, $id2)
    {
        $pivote = PackPresupuesto::where('presupuesto_id', $id1)->where('pack_id', $id2)->first();
        if ($pivote != null) {
            $this->datos_pack[$id1][$id2]['sueldos_monitores'] = json_decode($pivote->sueldos_monitores);
        }
    }

    public function getMonitoresPack($id1, $id2)
    {
        $pivote = PackPresupuesto::where('presupuesto_id', $id1)->where('pack_id', $id2)->first();
        if ($pivote != null) {
            if (empty(json_decode($pivote->id_monitores))) {
                foreach (ServicioPack::where('id', $id2)->first()->servicios()->get() as $servicioIndex => $itemServicio) {
                    for ($i = 0; $i < $pivote->numero_monitores[$servicioIndex]; $i++) {
                        $this->datos_pack[$id1][$id2]['id_monitores'][$servicioIndex][$i] = 0;
                    }
                }
            } else {
                $this->datos_pack[$id1][$id2]['id_monitores'] = json_decode($pivote->id_monitores);
            }
        }
    }

    public function getPagosPendientesPack($id1, $id2)
    {
        $pivote = PackPresupuesto::where('presupuesto_id', $id1)->where('pack_id', $id2)->first();
        if ($pivote != null) {
            $this->datos_pack[$id1][$id2]['pagos_pendientes'] = json_decode($pivote->pagos_pendientes);
        }
    }

    public function getCliente($id)
    {
        return $this->clientes->firstWhere('id', $this->presupuestos->firstWhere('id_evento', $id)->id_cliente)->nombre . " " . $this->clientes->firstWhere('id', $this->presupuestos->firstWhere('id_evento', $id)->id_cliente)->apellido;
    }
    public function cambioMonitorServicio($id1, $id2)
    {
        $pivote = ServicioPresupuesto::where('presupuesto_id', $id1)->where('servicio_id', $id2)->first();
        $pivote->update([
            'id_monitores' => json_encode($this->datos_servicio[$id1][$id2]['id_monitores']),
            'sueldo_monitores' => json_encode($this->datos_servicio[$id1][$id2]['sueldo_monitores']),
            'pago_pendiente' => json_encode($this->datos_servicio[$id1][$id2]['pago_pendiente'])
        ]);
    }

    public function cambioMonitorPack($id1, $id2)
    {
        $pivote = PackPresupuesto::where('presupuesto_id', $id1)->where('pack_id', $id2)->first();
        $pivote->update([
            'id_monitores' => json_encode($this->datos_pack[$id1][$id2]['id_monitores']),
            'sueldos_monitores' => json_encode($this->datos_pack[$id1][$id2]['sueldos_monitores']),
            'pagos_pendientes' => json_encode($this->datos_pack[$id1][$id2]['pagos_pendientes'])
        ]);
    }
}
