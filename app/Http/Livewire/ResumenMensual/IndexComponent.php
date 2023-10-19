<?php

namespace App\Http\Livewire\ResumenMensual;

use App\Models\Evento;
use App\Models\Presupuesto;
use App\Models\Gastos;
use App\Models\TipoGasto;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;

class IndexComponent extends Component
{
    // public $search;

    public $mes;
    public $presupuestos;
    public $ingresos_brutos = [];
    public $liquido_semanal = [];
    public $adelantos = [];
    public $pendiente_cobrar = [];
    public $salario_monitores = [];
    public $pte_pago_monitores = [];
    public $gastos_gasoil = [];
    public $seguros_sociales = [];
    public $material_fungible = [];
    public $total_gastos = [];
    public $gastos;
    public $otros_gastos = [];
    public $otros_gastos_total = [];
    public $semana_curso;
    public $servicios;
    public $monitores;
    public $intervalosSemanas = [];
    public $intervalosSemanasFechas = [];

    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->gastos = Gastos::all();
        $this->tipos_gasto = TipoGasto::all();
    }


    public function render()
    {

        return view('livewire.resumen-mensual.index-component');
    }
    public function cambioMes()
    {

        $fechaInicio = Carbon::createFromFormat('Y-m', $this->mes)->startOfMonth();

        // Si el primer día del mes no es un lunes, retrocedemos a la fecha del lunes anterior
        if ($fechaInicio->dayOfWeekIso !== 1) {
            $fechaInicio->previous(Carbon::MONDAY);
        }

        $fechaFinMes = Carbon::createFromFormat('Y-m', $this->mes)->endOfMonth()->endOfWeek();

        $this->intervalosSemanas = [];

        while ($fechaInicio <= $fechaFinMes) {
            $inicioSemana = $fechaInicio->copy();
            $finSemana = $fechaInicio->endOfWeek();

            // Si el fin de semana excede el fin de mes, lo ajustamos al último día del mes
            if ($finSemana > $fechaFinMes) {
                $finSemana = $fechaFinMes;
            }

            $this->intervalosSemanas[] = [
                'inicio' => $inicioSemana->toDateString(),
                'fin' => $finSemana->toDateString()
            ];

            $fechaInicio->addWeek()->startOfWeek();
        }
        $this->calcularDatosSemanales();
    }

    public function calcularDatosSemanales()
    {
        foreach ($this->intervalosSemanas as $intervaloIndex => $intervalo) {
            if (Carbon::now()->between($intervalo['inicio'], $intervalo['fin'])) {
                $this->semana_curso = $intervaloIndex + 1;
            }
            $this->ingresos_brutos[$intervaloIndex] = $this->presupuestos->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])->sum('precioFinal');
            $this->adelantos[$intervaloIndex] = $this->presupuestos->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])->sum('adelanto');
            $this->liquido_semanal[$intervaloIndex] = ($this->ingresos_brutos[$intervaloIndex] - $this->adelantos[$intervaloIndex]);
            $this->pendiente_cobrar[$intervaloIndex] = $this->presupuestos->where('estado', 'Facturado')->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])->sum('precioFinal');
            //Salario de monitores
            // Parte 1: Packs
            $sumaPacks = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->packs->flatMap(function ($pack) {
                        return json_decode($pack->pivot->sueldos_monitores, true);  // Asumiendo que "sueldos" es el nombre de la columna JSON en la tabla pivot
                    });
                })
                ->flatten(1)
                ->sum();

            // Parte 2: Servicios
            $sumaServicios = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->servicios->pluck('pivot.sueldo_monitores');  // Asumiendo que "sueldos" es el nombre de la columna en la tabla pivot
                })
                ->map(function ($sueldo) {
                    return (int) $sueldo;  // Convierte el string a un integer
                })
                ->sum();

            $this->salario_monitores[$intervaloIndex] = $sumaPacks + $sumaServicios;

            $pagoPacks = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->packs->flatMap(function ($pack) {
                        return json_decode($pack->pivot->pagos_pendientes, true);  // Asumiendo que "sueldos" es el nombre de la columna JSON en la tabla pivot
                    });
                })
                ->flatten(1)
                ->sum();

            // Parte 2: Servicios
            $pagoServicios = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->servicios->pluck('pivot.pago_pendiente');  // Asumiendo que "sueldos" es el nombre de la columna en la tabla pivot
                })
                ->map(function ($sueldo) {
                    return (int) $sueldo;  // Convierte el string a un integer
                })
                ->sum();

            $this->pte_pago_monitores[$intervaloIndex] = $pagoPacks + $pagoServicios;

            $gastoPacks = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->packs->flatMap(function ($pack) {
                        return json_decode($pack->pivot->gastos_gasoil, true);  // Asumiendo que "sueldos" es el nombre de la columna JSON en la tabla pivot
                    });
                })
                ->flatten(1)
                ->sum();

            // Parte 2: Servicios
            $gastoServicios = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->servicios->pluck('pivot.gasto_gasoil');  // Asumiendo que "sueldos" es el nombre de la columna en la tabla pivot
                })
                ->map(function ($gasto) {
                    return (int) $gasto;  // Convierte el string a un integer
                })
                ->sum();

            $this->gastos_gasoil[$intervaloIndex] = $gastoPacks + $gastoServicios;

            $segPacks = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->packs->flatMap(function ($pack) {
                        return json_decode($pack->pivot->gastos_gasoil, true);  // Asumiendo que "sueldos" es el nombre de la columna JSON en la tabla pivot
                    });
                })
                ->flatten(1)
                ->sum();

            // Parte 2: Servicios
            $segServicios = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->servicios->pluck('pivot.gasto_gasoil');  // Asumiendo que "sueldos" es el nombre de la columna en la tabla pivot
                })
                ->map(function ($sueldo) {
                    return (int) $sueldo;  // Convierte el string a un integer
                })
                ->sum();

            $this->seguros_sociales[$intervaloIndex] = $segPacks + $segServicios;

            $matPacks = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->packs->flatMap(function ($pack) {
                        return json_decode($pack->pivot->gastos_gasoil, true);  // Asumiendo que "sueldos" es el nombre de la columna JSON en la tabla pivot
                    });
                })
                ->flatten(1)
                ->sum();

            // Parte 2: Servicios
            $matServicios = $this->presupuestos
                ->whereBetween('fechaEmision', [$intervalo['inicio'], $intervalo['fin']])
                ->flatMap(function ($presupuesto) {
                    return $presupuesto->servicios->pluck('pivot.gasto_gasoil');  // Asumiendo que "sueldos" es el nombre de la columna en la tabla pivot
                })
                ->map(function ($sueldo) {
                    return (int) $sueldo;  // Convierte el string a un integer
                })
                ->sum();

            $this->material_fungible[$intervaloIndex] = $matPacks + $matServicios;

            $this->total_gastos[$intervaloIndex] = $this->salario_monitores[$intervaloIndex] + $this->gastos_gasoil[$intervaloIndex] + $this->seguros_sociales[$intervaloIndex] + $this->material_fungible[$intervaloIndex];
            $this->otros_gastos[$intervaloIndex] = Gastos::select('tipo_gasto', DB::raw('SUM(cuantia) as total'))->groupBy('tipo_gasto')->whereBetween('date', [$intervalo['inicio'], $intervalo['fin']])->get();
            $this->otros_gastos_total[$intervaloIndex] = $this->otros_gastos[$intervaloIndex]->pluck('total')->sum();


            $date_inicio = Carbon::parse($intervalo['inicio']);
            $date_fin = Carbon::parse($intervalo['fin']);
            $formattedDate_inicio = $date_inicio->isoFormat('dddd, D [de] MMMM [de] Y');
            $formattedDate_fin = $date_fin->isoFormat('dddd, D [de] MMMM [de] Y');
            $this->intervalosSemanasFechas[$intervaloIndex]['inicio'] = str_replace('s畸bado', 'sábado', $formattedDate_inicio);
            $this->intervalosSemanasFechas[$intervaloIndex]['fin'] = str_replace('s畸bado', 'sábado', $formattedDate_fin);
        }
    }
}
