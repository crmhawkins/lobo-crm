<?php

namespace App\Http\Livewire\ResumenSemanas;

use App\Models\ServicioPack;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Programa;
use App\Models\Evento;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Monitor;
use App\Models\PackPresupuesto;
use App\Models\Presupuesto;
use App\Models\Servicio;
use App\Models\ServicioEvento;
use App\Models\TipoEvento;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\ServicioPresupuesto;
use Carbon\CarbonPeriod;

class ShowComponent extends Component
{
    use LivewireAlert;
    // public $search;
    public $dias = [];
    public $semana;
    public $monitores_datos;
    public $fechas = [];
    public $presupuestos;
    public $contratos;
    public $eventos;
    public $categorias;
    public $servicios;
    public $packs;

    public $datoEdicion = [
        'id' => null,     // ID del elemento en edición
        'column' => null, // Columna (o tipo de dato) en edición
        'value' => null,  // Valor actual en edición
    ];

    protected $listeners = [
        'loadWeek' => 'loadWeek',
        'loadMonth' => 'loadMonth',
        'terminarInputs'
    ];


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->contratos = Contrato::all();
        $this->servicios = Servicio::all();
        $this->packs = ServicioPack::all();
        $this->monitores_datos = Monitor::all();
        $this->categorias = TipoEvento::all();
        $this->semana = Carbon::now()->year . "-W" . Carbon::now()->weekOfYear;
        $this->cambioSemana();
    }


    public function render()
    {

        return view('livewire.resumen-semana.show-component');
    }

    public function checkAuthContrato($idEvento)
    {
        $presupuesto_check = $this->presupuestos->firstWhere('id_evento', $idEvento);
        if ($presupuesto_check != null) {
            $contrato_check = $this->contratos->firstWhere('id_presupuesto', $presupuesto_check->id);
            if ($contrato_check != null) {
                if ($contrato_check->authImagen == 1) {
                    return 'Autoriza imagen';
                } else {
                    return 'No autoriza imagen';
                }
            } else {
                return 'Sin contrato';
            }
        } else {
            return '¿No hay presupuesto?';
        }
    }

    public function checknPresupuesto($idEvento)
    {
        $presupuesto_check = $this->presupuestos->firstWhere('id_evento', $idEvento);
        if ($presupuesto_check != null) {
            return $presupuesto_check->nPresupuesto;
        } else {
            return '¿No hay presupuesto?';
        }
    }
    public function checkPrecioFinal($idEvento)
    {
        $presupuesto_check = $this->presupuestos->firstWhere('id_evento', $idEvento);
        if ($presupuesto_check != null) {
            return $presupuesto_check->nPresupuesto;
        } else {
            return '¿No hay presupuesto?';
        }
    }

    public function cambioSemana()
    {
        $this->fechas = [];
        $this->dias = [];

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
    }

    public function detectarEdicion($id, $column)
    {
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;

        $this->datoEdicion['id'] = $id;
        $this->datoEdicion['column'] = $column;
        switch ($this->datoEdicion['column']) {
            case 'eventoNombre':
                $this->datoEdicion['value'] = $this->eventos->firstWhere('id', $id)->$column;
                break;
            case 'precioFinal':
                $this->datoEdicion['value'] = $this->presupuestos->firstWhere('id_evento', $id)->$column;
                break;
            case 'eventoNiños':
                $this->datoEdicion['value'] = $this->eventos->firstWhere('id', $id)->$column;
                break;
            case 'eventoAdulto':
                $this->datoEdicion['value'] = $this->eventos->firstWhere('id', $id)->eventoAdulto;
                break;
            default:
                # code...
                break;
        }
    }

    public function detectarEdicionServicio($id, $id2, $column)
    {
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;

        $this->datoEdicion['id'] = ['presupuesto' => $id, 'servicio' => $id2];
        $this->datoEdicion['column'] = $column;
        switch ($this->datoEdicion['column']) {
            case 'servicioNombre':
                $this->datoEdicion['value'] = $this->servicios->firstWhere('id', $id2)->id;
                break;
            case 'servicioHoraMontaje':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->hora_montaje;
                break;
            case 'servicioHoraInicio':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->hora_inicio;
                break;
            case 'servicioTiempo':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->tiempo;
                break;
            case 'servicioHoraTiempoMontaje':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->tiempo_montaje;
                break;
            default:
                # code...
                break;
        }
    }

    public function terminarInputs()
    {
        if ($this->datoEdicion['id'] != null) {
            if (isset($this->datoEdicion['id']['servicio'])) {
                if (isset($this->datoEdicion['id']['pack'])) {
                    if (isset($this->datoEdicion['id']['monitor'])) {
                        $this->terminarEdicionMonitoresPack();
                    } else {
                        $this->terminarEdicionServicioPack();
                    }
                } else {
                    if (isset($this->datoEdicion['id']['monitor'])) {
                        $this->terminarEdicionServicioMonitores();
                    } else {
                        $this->terminarEdicionServicio();
                    }
                }
            } else {
                $this->terminarEdicion();
            }
        }
    }

    public function detectarEdicionPack($id, $id2, $id3, $id4, $column)
    {
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;

        $this->datoEdicion['id'] = ['presupuesto' => $id, 'pack' => $id2, 'servicio' => $id3];
        $this->datoEdicion['column'] = $column;
        switch ($this->datoEdicion['column']) {
            case 'packNombre':
                $this->datoEdicion['value'] = $this->packs->firstWhere('id', $id2)->id;
                break;
            case 'packHoraMontaje':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->horas_montaje)[$id3];
                break;
            case 'packHoraInicio':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] =  json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->horas_inicio)[$id3];
                break;
            case 'packTiempo':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] =  json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->tiempos)[$id3];
                break;
            case 'packHoraTiempoMontaje':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $this->datoEdicion['value'] =  json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->tiempos_montaje)[$id3];
                break;

            default:
                # code...
                break;
        }
    }
    public function detectarEdicionMonitores($id, $id2, $id3, $column)
    {
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;

        $this->datoEdicion['id'] = ['presupuesto' => $id, 'servicio' => $id2, 'monitor' => $id3];
        $this->datoEdicion['column'] = $column;
        switch ($this->datoEdicion['column']) {
            case 'monitorNombre':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->id_monitores, true);
                $this->datoEdicion['value'] = $monitores[$id3];
                break;
            case 'sueldoMonitor':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->sueldo_monitores, true);
                $this->datoEdicion['value'] = $monitores[$id3];
                break;
            case 'gasto_gasoil':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->gasto_gasoil, true);
                $this->datoEdicion['value'] = $monitores[$id3];
                break;
            default:
                # code...
                break;
        }
    }
    public function detectarEdicionMonitoresPack($id, $id2, $id3, $id4, $column)
    {
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;

        $this->datoEdicion['id'] = ['presupuesto' => $id, 'pack' => $id2, 'servicio' => $id3, 'monitor' => $id4];
        $this->datoEdicion['column'] = $column;
        switch ($this->datoEdicion['column']) {
            case 'monitorNombrePack':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->id_monitores, true);
                $this->datoEdicion['value'] = $monitores[$id3][$id4];
                break;
            case 'sueldoMonitorPack':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->sueldos_monitores, true);
                $this->datoEdicion['value'] = $monitores[$id3][$id4];
                break;
            case 'gasto_gasoilPack':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $id)->id;
                $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->gastos_gasoil, true);
                $this->datoEdicion['value'] = $monitores[$id3][$id4];
                break;
            default:
                # code...
                break;
        }
    }

    public function terminarEdicion()
    {
        switch ($this->datoEdicion['column']) {
            case 'eventoNombre':
                $this->eventos->firstWhere('id', $this->datoEdicion['id'])->update(['eventoNombre' => $this->datoEdicion['value']]);
                break;
            case 'precioFinal':
                $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id'])->update(['precioFinal' => $this->datoEdicion['value']]);
                break;
            case 'eventoNiños':
                $this->eventos->firstWhere('id', $this->datoEdicion['id'])->update(['eventoNiños' => $this->datoEdicion['value']]);
                break;
            case 'eventoAdulto':
                $this->eventos->firstWhere('id', $this->datoEdicion['id'])->update(['eventoAdulto' => $this->datoEdicion['value']]);
                break;
            default:
                # code...
                break;
        }
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;
        $reload;
    }

    public function terminarEdicionServicio()
    {
        if ($this->datoEdicion['id'] != null) {
            switch ($this->datoEdicion['column']) {
                case 'servicioNombre':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['servicio_id' => $this->datoEdicion['value']]);
                    break;
                case 'servicioHoraMontaje':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['hora_montaje' => $this->datoEdicion['value']]);
                    break;
                case 'servicioHoraInicio':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['hora_inicio' => $this->datoEdicion['value']]);
                    break;
                case 'servicioTiempo':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['tiempo' => $this->datoEdicion['value']]);
                    break;
                case 'servicioHoraTiempoMontaje':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['tiempo_montaje' => $this->datoEdicion['value']]);
                    break;
                default:
                    # code...
                    break;
            }
        }

        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;
        $reload;
    }
    public function terminarEdicionMonitores()
    {
        switch ($this->datoEdicion['column']) {
            case 'monitorNombre':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->id_monitores, true);
                $monitores[$this->datoEdicion['id']['monitor']] = $this->datoEdicion['value'];
                $monitoresSave = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['id_monitores' => json_encode($monitores)]);
                break;
            case 'sueldoMonitor':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->sueldo_monitores, true);
                $monitores[$this->datoEdicion['id']['monitor']] = $this->datoEdicion['value'];
                $monitoresSave = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['sueldo_monitores' => json_encode($monitores)]);
                break;
            case 'gasto_gasoil':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->gasto_gasoil, true);
                $monitores[$this->datoEdicion['id']['monitor']] = $this->datoEdicion['value'];
                $monitoresSave = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['gasto_gasoil' => json_encode($monitores)]);
                break;
            default:
                # code...
                break;
        }
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;
        $reload;
    }
    public function terminarEdicionServicioPack()
    {
        if ($this->datoEdicion['id'] != null) {
            switch ($this->datoEdicion['column']) {
                case 'packHoraMontaje':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->horas_montaje, true);
                    $monitores[$this->datoEdicion['id']['servicio']] = $this->datoEdicion['value'];
                    $monitoresSave = PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->update(['horas_montaje' => json_encode($monitores)]);
                    break;
                case 'packHoraInicio':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->horas_inicio, true);
                    $monitores[$this->datoEdicion['id']['servicio']] = $this->datoEdicion['value'];
                    $monitoresSave = PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->update(['horas_inicio' => json_encode($monitores)]);
                    break;
                case 'servicioTiempo':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['tiempo' => $this->datoEdicion['value']]);
                    break;
                case 'servicioHoraTiempoMontaje':
                    $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                    ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['tiempo_montaje' => $this->datoEdicion['value']]);
                    break;
                default:
                    # code...
                    break;
            }
        }

        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;
        $reload;
    }
    public function terminarEdicionMonitoresPack()
    {
        switch ($this->datoEdicion['column']) {
            case 'monitorNombrePack':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->id_monitores, true);
                $monitores[$this->datoEdicion['id']['servicio']][$this->datoEdicion['id']['monitor']] = $this->datoEdicion['value'];
                $monitoresSave = PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->update(['id_monitores' => json_encode($monitores)]);
                break;
            case 'sueldoMonitorPack':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                $monitores = json_decode(PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->sueldos_monitores, true);
                $monitores[$this->datoEdicion['id']['servicio']][$this->datoEdicion['id']['monitor']] = $this->datoEdicion['value'];
                $monitoresSave = PackPresupuesto::where('presupuesto_id', $id_presupuesto)->where('pack_id', $this->datoEdicion['id']['pack'])->first()->update(['sueldos_monitores' => json_encode($monitores)]);
                break;
            case 'gasto_gasoil':
                $id_presupuesto = $this->presupuestos->firstWhere('id_evento', $this->datoEdicion['id']['presupuesto'])->id;
                $monitores = json_decode(ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->gasto_gasoil, true);
                $monitores[$this->datoEdicion['id']['monitor']] = $this->datoEdicion['value'];
                $monitoresSave = ServicioPresupuesto::where('presupuesto_id', $id_presupuesto)->where('servicio_id', $this->datoEdicion['id']['servicio'])->first()->update(['gasto_gasoil' => json_encode($monitores)]);
                break;
            default:
                # code...
                break;
        }
        $this->datoEdicion['id'] = null;
        $this->datoEdicion['column'] = null;
        $this->datoEdicion['value'] = null;
        $reload;
    }
    public function getMonitor($id)
    {
        if ($this->monitores_datos->find($id) == null) {
            return "";
        } else {
            return $this->monitores_datos->find($id)->alias;
        }
    }
}
