<?php

namespace App\Http\Livewire\Caja;

use App\Models\Settings;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Proveedores;
use App\Models\Facturas;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Caja;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Delegacion;
use App\Models\FacturasCompensadas;
use Livewire\WithPagination;
use App\Models\CuadroFlujo;

class Cuadro extends Component
{
    use LivewireAlert, WithPagination;

    public $dailyTransactions = [];
    public $selectedMonth;

    public $saldo_inicial_caixa;
    public $saldo_inicial_santander;

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->initializeSaldos();
        $this->dailyTransactions = $this->getDailyTransactions();
    }

    public function updatedSelectedMonth()
    {
        $this->initializeSaldos();
        $this->dailyTransactions = $this->getDailyTransactions();
    }

    protected function initializeSaldos()
    {
        $mes = Carbon::parse($this->selectedMonth)->month;
        $anio = Carbon::parse($this->selectedMonth)->year;

        $cuadroFlujoCaixa = CuadroFlujo::getOrCreateForMonth($mes, $anio, 1);
        $cuadroFlujoSantander = CuadroFlujo::getOrCreateForMonth($mes, $anio, 2);

        if ($cuadroFlujoCaixa->saldo_inicial == 0) {
            $cuadroFlujoCaixa->saldo_inicial = $this->calculateSaldoInicial($mes, $anio, 1);
            $cuadroFlujoCaixa->save();
        }

        if ($cuadroFlujoSantander->saldo_inicial == 0) {
            $cuadroFlujoSantander->saldo_inicial = $this->calculateSaldoInicial($mes, $anio, 2);
            $cuadroFlujoSantander->save();
        }

        $this->saldo_inicial_caixa = $cuadroFlujoCaixa->saldo_inicial;
        $this->saldo_inicial_santander = $cuadroFlujoSantander->saldo_inicial;
    }

    protected function calculateSaldoInicial($mes, $anio, $banco_id)
    {
        $previousMonth = Carbon::create($anio, $mes)->subMonth();
        $previousCuadroFlujo = CuadroFlujo::where('mes', $previousMonth->month)
            ->where('anio', $previousMonth->year)
            ->where('banco_id', $banco_id)
            ->first();

        if ($previousCuadroFlujo) {
            return $previousCuadroFlujo->saldo_final;
        }

        $ingresos = Caja::where('banco', $banco_id)
            ->where('tipo_movimiento', 'Ingreso')
            ->where('fecha', '<', Carbon::create($anio, $mes)->startOfMonth())
            ->sum('importe');

        $gastos = Caja::where('banco', $banco_id)
            ->where('tipo_movimiento', 'Gasto')
            ->where('fecha', '<', Carbon::create($anio, $mes)->startOfMonth())
            ->sum('importe');

        return $ingresos - $gastos;
    }

    public function recalculateSaldos()
    {
        $mes = Carbon::parse($this->selectedMonth)->month;
        $anio = Carbon::parse($this->selectedMonth)->year;

        // Recalcular saldo inicial para Caixa
        $cuadroFlujoCaixa = CuadroFlujo::getOrCreateForMonth($mes, $anio, 1);
        $cuadroFlujoCaixa->saldo_inicial = $this->calculateSaldoInicialDirectly($mes, $anio, 1);
        $cuadroFlujoCaixa->save();

        // Recalcular saldo inicial para Santander
        $cuadroFlujoSantander = CuadroFlujo::getOrCreateForMonth($mes, $anio, 2);
        $cuadroFlujoSantander->saldo_inicial = $this->calculateSaldoInicialDirectly($mes, $anio, 2);
        $cuadroFlujoSantander->save();

        // Actualizar las propiedades del componente
        $this->saldo_inicial_caixa = $cuadroFlujoCaixa->saldo_inicial;
        $this->saldo_inicial_santander = $cuadroFlujoSantander->saldo_inicial;
    }

    protected function calculateSaldoInicialDirectly($mes, $anio, $banco_id)
    {
        $ingresos = Caja::where('banco', $banco_id)
            ->where('tipo_movimiento', 'Ingreso')
            ->where('fecha', '<', Carbon::create($anio, $mes)->startOfMonth())
            ->sum('importe');

        $gastos = Caja::where('banco', $banco_id)
            ->where('tipo_movimiento', 'Gasto')
            ->where('fecha', '<', Carbon::create($anio, $mes)->startOfMonth())
            ->sum('importe');

        return $ingresos - $gastos;
    }

    protected function getDailyTransactions()
    {
        $transactions = Caja::whereYear('fecha', Carbon::parse($this->selectedMonth)->year)
            ->whereMonth('fecha', Carbon::parse($this->selectedMonth)->month)
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->fecha)->format('Y-m-d');
            });

        $dailyTransactions = [];

        foreach ($transactions as $date => $records) {
            $dailyTransactions[$date] = [
                'banco1' => [
                    'ingresos' => collect($records->where('banco', 1)->where('tipo_movimiento', 'Ingreso')->values()),
                    'gastos' => collect($records->where('banco', 1)->where('tipo_movimiento', 'Gasto')->values()),
                ],
                'banco2' => [
                    'ingresos' => collect($records->whereIn('banco', [2, null])->where('tipo_movimiento', 'Ingreso')->values()),
                    'gastos' => collect($records->whereIn('banco', [2, null])->where('tipo_movimiento', 'Gasto')->values()),
                ],
            ];
        }

        return collect($dailyTransactions)->sortKeys();
    }

    public function render()
    {
        return view('livewire.caja.cuadro-flujo-component', [
            'dailyTransactions' => $this->dailyTransactions,
            'selectedMonth' => $this->selectedMonth,
        ]);
    }

    public function updateSaldoInicial($banco)
    {
        $mes = Carbon::parse($this->selectedMonth)->month;
        $anio = Carbon::parse($this->selectedMonth)->year;

        if ($banco === 'caixa') {
            $cuadroFlujo = CuadroFlujo::getOrCreateForMonth($mes, $anio, 1);
            $cuadroFlujo->saldo_inicial = $this->saldo_inicial_caixa;
        } else if ($banco === 'santander') {
            $cuadroFlujo = CuadroFlujo::getOrCreateForMonth($mes, $anio, 2);
            $cuadroFlujo->saldo_inicial = $this->saldo_inicial_santander;
        }

        $cuadroFlujo->save();


        $this->dailyTransactions = $this->getDailyTransactions();
    }

    public function saveAndReload()
    {
        $this->updateSaldoInicial('caixa');
        $this->updateSaldoInicial('santander');
        return redirect()->to(request()->header('Referer'));
    }
}
