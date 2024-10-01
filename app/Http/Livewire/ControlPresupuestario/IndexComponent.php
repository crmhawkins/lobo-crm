<?php

namespace App\Http\Livewire\ControlPresupuestario;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Delegacion;
use App\Models\Facturas;
use Illuminate\Support\Facades\DB;
use App\Models\Caja;

//collection
use Illuminate\Support\Collection;

class IndexComponent extends Component
{
    public $delegaciones;
    public $year;
    public $totalesPorDelegacionYTrimestre;
    public $totalesPorDelegacionYTrimestreCompras;
    public $resultadosPorDelegacion;

    public function mount()
    {
        // Obtener todas las delegaciones
        $this->delegaciones = Delegacion::all();
        $this->filtrarVentas();
        $this->filtrarCompras();
        $this->calcularResultado();

    }

    public function filtrarVentas()
{
    $query = Facturas::query();

    // Obtener el año seleccionado (si no se selecciona, tomamos el año actual)
    $year = $this->year ?? now()->year;

    // Definir los periodos de cada trimestre
    $trimestres = [
        '1º T' => [Carbon::create($year, 1, 1), Carbon::create($year, 3, 31)],
        '2º T' => [Carbon::create($year, 4, 1), Carbon::create($year, 6, 30)],
        '3º T' => [Carbon::create($year, 7, 1), Carbon::create($year, 9, 30)],
        '4º T' => [Carbon::create($year, 10, 1), Carbon::create($year, 12, 31)],
    ];

    // Filtrar las facturas del año seleccionado y solo las pagadas
    $query->whereYear('fecha_emision', $year);
    $query->where('estado', 'Pagado');

    // Obtener todas las facturas del año
    $facturas = $query->get();

    // Inicializar un array para almacenar los totales por delegación y trimestre
    $totalesPorDelegacionYTrimestre = [];

    // Iterar sobre cada trimestre
    foreach ($trimestres as $trimestre => $periodo) {
        // Inicializar el total del trimestre en 0
        $totalGeneralTrimestre = 0;

        // Iterar sobre cada delegación
        foreach ($this->delegaciones as $delegacion) {
            // Filtrar las facturas de la delegación actual
            $facturasDelegacion = $facturas->filter(function ($factura) use ($delegacion) {
                return $factura->cliente->delegacion_COD == $delegacion->COD;
            });

            // Filtrar las facturas del trimestre actual
            $facturasTrimestre = $facturasDelegacion->filter(function ($factura) use ($periodo) {
                return $factura->fecha_emision >= $periodo[0] && $factura->fecha_emision <= $periodo[1];
            });

            // Calcular el total de las facturas del trimestre actual para la delegación actual
            $totalTrimestre = $facturasTrimestre->sum('total');

            // Acumular el total de la delegación en el total del trimestre
            $totalGeneralTrimestre += $totalTrimestre;

            // Almacenar el total en el array de totales con las claves trimestre y delegacion_COD
            $totalesPorDelegacionYTrimestre[$trimestre][$delegacion->COD] = $totalTrimestre;
        }

        // Almacenar el total general del trimestre
        $totalesPorDelegacionYTrimestre[$trimestre]['total_general'] = $totalGeneralTrimestre;
    }

    // Almacenar el resultado en la propiedad del componente
    $this->totalesPorDelegacionYTrimestre = $totalesPorDelegacionYTrimestre;
}

public function filtrarCompras()
{
    $query = Caja::query();

    // Obtener el año seleccionado (si no se selecciona, tomamos el año actual)
    $year = $this->year ?? now()->year;

    // Definir los periodos de cada trimestre
    $trimestres = [
        '1º T' => [Carbon::create($year, 1, 1), Carbon::create($year, 3, 31)],
        '2º T' => [Carbon::create($year, 4, 1), Carbon::create($year, 6, 30)],
        '3º T' => [Carbon::create($year, 7, 1), Carbon::create($year, 9, 30)],
        '4º T' => [Carbon::create($year, 10, 1), Carbon::create($year, 12, 31)],
    ];

    // Filtrar las compras del año seleccionado y que el tipo de movimiento sea "Gasto"
    $query->whereYear('fecha', $year);
    $query->where('tipo_movimiento', 'Gasto');

    // Obtener todas las compras del año
    $compras = $query->get();


    // Inicializar un array para almacenar los totales por delegación y trimestre
    $totalesPorDelegacionYTrimestreCompras = [];

    // Iterar sobre cada trimestre
    foreach ($trimestres as $trimestre => $periodo) {
        // Inicializar el total del trimestre en 0
        $totalGeneralTrimestre = 0;

        // Iterar sobre cada delegación
        foreach ($this->delegaciones as $delegacion) {
            // Filtrar las compras de la delegación actual
            $comprasDelegacion = $compras->filter(function ($compra) use ($delegacion) {
                return $compra->delegacion_id == $delegacion->id;
            });

            // Filtrar las compras del trimestre actual
            $comprasTrimestre = $comprasDelegacion->filter(function ($compra) use ($periodo) {
                return $compra->fecha >= $periodo[0] && $compra->fecha <= $periodo[1];
            });

            // Calcular el total de las compras del trimestre actual para la delegación actual
            $totalTrimestre = $comprasTrimestre->sum('total');

            // Acumular el total de la delegación en el total del trimestre
            $totalGeneralTrimestre += $totalTrimestre;

            // Almacenar el total en el array de totales con las claves trimestre y delegacion_id
            $totalesPorDelegacionYTrimestreCompras[$trimestre][$delegacion->id] = $totalTrimestre;
        }

        // Almacenar el total general del trimestre
        $totalesPorDelegacionYTrimestreCompras[$trimestre]['total_general'] = $totalGeneralTrimestre;
    }

    // Almacenar el resultado en una propiedad del componente
    $this->totalesPorDelegacionYTrimestreCompras = $totalesPorDelegacionYTrimestreCompras;
}

    
    // Sumar las ventas y restar las compras para calcular el resultado (A - B)

public function calcularResultado()
{
    $resultadosPorDelegacion = [];
    $totalesPorDelegacionYTrimestre = $this->totalesPorDelegacionYTrimestre;
    $totalesPorDelegacionYTrimestreCompras = $this->totalesPorDelegacionYTrimestreCompras;

    foreach ($this->delegaciones as $delegacion) {
        if ($delegacion->nombre != '00 GENERAL GLOBAL') {
            // Obtener las ventas por delegación
            $totalVentas = 0;
            foreach ($totalesPorDelegacionYTrimestre as $totalesVentas) {
                $totalVentas += $totalesVentas[$delegacion->COD] ?? 0;
            }

            // Obtener las compras por delegación
            $totalCompras = 0;
            foreach ($totalesPorDelegacionYTrimestreCompras as $totalesCompras) {
                $totalCompras += $totalesCompras[$delegacion->id] ?? 0;
            }

            // Calcular el resultado (Ventas - Compras)
            $resultado = $totalVentas - $totalCompras;
            $resultadosPorDelegacion[$delegacion->COD] = $resultado;
        }
    }

    $this->resultadosPorDelegacion = $resultadosPorDelegacion;
}


    

    public function render()
    {
        return view('livewire.control-presupuestario.index-component', [
            'totalesPorDelegacionYTrimestre' => $this->totalesPorDelegacionYTrimestre,
        ]);
    }
}
