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
use App\Models\Bancos;

class Cuadro extends Component
{
    use LivewireAlert, WithPagination;

    public $dailyTransactions = [];
    public $selectedMonth;

    public $saldo_inicial_caixa;
    public $saldo_inicial_santander;



    public $is_pagado;
    public $banco;
    public $fecha;
    public $importe = 0;
    public $descripcion;
    public $tipoMovimiento;
    public $pedido_id;

    public $bancos;

    public $facturaSeleccionada;
    public $importeFactura;
    public $ingresos_factura;
    public $facturas_compensadas;
    public $importeFacturaCompensada;
    public $importeCompensado;
    public $compensacion_factura = false;
    public $movimientoId;
    public $isPagadoEditar = false;

    public $proveedores = [];
    public $proveedor_id;

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->initializeSaldos();
        $this->dailyTransactions = $this->getDailyTransactions();
        $this->facturas = Facturas::where(function($query) {
            $query->where('estado', 'Pendiente')
                  ->orWhere('estado', 'Parcial');
        })
        ->whereNull('factura_id')
        ->orderBy('id', 'asc')
        ->get();
        $this->bancos = Bancos::all();
        $this->proveedores = Proveedores::orderBy('nombre', 'asc')->get();
    }

    public function facturaHasIva($id)
    {
        $factura = Facturas::find($id);
        //dd($factura);
        //dependiendo de que delegacion sea el cliente se le aplica iva o no
        if(!$factura){

            //return alert error
            $this->alert('error', '¡No se ha podido cargar la factura!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);

            return false;


        }
        $delegacion = $this->getDelegacion($factura->cliente_id);
        if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR'){
            return false;
        }else{
            return true;
        }
    }

    public function getDelegacion($id)
    {
        $delegaciones = Delegacion::all();
        $cliente = Clients::find($id);
        if (isset($cliente)) {
            return $delegaciones->where('COD', $cliente->delegacion_COD)->first() ? $delegaciones->where('COD', $cliente->delegacion_COD)->first()->nombre : "no definido";
        }
        return "no definido";
    }

    public function pedidoSelected($pedidoId)
    {
        $factura = Facturas::find($pedidoId);
        if ($factura) {
            $this->importe = $factura->importe;
        }
    }

    public function editarMovimiento($id)
{
    $this->movimientoId = $id;
    $movimiento = Caja::find($id);
    
    if ($movimiento) {
        $this->fechaMovimiento = $movimiento->fecha;
        $this->isPagadoEditar = $movimiento->is_pagado;
    }
    $this->dailyTransactions = $this->getDailyTransactions();

    $this->dispatchBrowserEvent('abrirModalEditar');
}

public function actualizarMovimiento()
{
    $movimiento = Caja::find($this->movimientoId);
    
    if ($movimiento) {
        $movimiento->update([
            'fecha' => $this->fechaMovimiento,
            'is_pagado' => $this->isPagadoEditar
        ]);
    }
    $this->dailyTransactions = $this->getDailyTransactions();


    $this->dispatchBrowserEvent('cerrarModalEditar');
    $this->alert('success', 'Movimiento actualizado correctamente');
    $this->emit('movimientoActualizado');
}


    public function updatedPedidoId($id)
{
    if(isset($id) && $id != null){
        $this->facturaSeleccionada = Facturas::find($id);

        if($this->facturaHasIva($id)){
            $this->importeFactura = $this->facturaSeleccionada->total;
        }else{
            $this->importeFactura = $this->facturaSeleccionada->precio;
        }
        $this->ingresos_factura = Caja::where('pedido_id', $id)->get();
        $this->facturas_compensadas = FacturasCompensadas::where('factura_id', $id)->get();
        //dd( $this->facturas_compensadas);
        $total = 0;
        foreach ($this->facturas_compensadas as $factura) {
            
            $total += $factura->pagado;
       
        }

        //dd($this->ingresos_factura->sum('importe'));
        if(count($this->ingresos_factura) > 0){
            $this->importe = $this->importeFactura - $this->ingresos_factura->sum('importe');
            

        }else{
            $this->importe = $this->importeFactura;
        }
        
        if(count($this->facturas_compensadas) > 0){
            //dd("hola");
            $this->importeFacturaCompensada = $this->importe - $total;
            $this->importeCompensado = $total;
            $this->compensacion_factura = true;
        }else{
            $this->compensacion_factura = false;
        }

        //si esta factura tiene factura rectificativa
        $facturaRectificativa = Facturas::where('id' , $this->facturaSeleccionada->factura_rectificativa_id)->first();
        if($facturaRectificativa){
            //dd($facturaRectificativa);
            if($this->facturaHasIva($facturaRectificativa->id)){
                $this->importeFactura = $facturaRectificativa->total;
            }else{
                $this->importeFactura = $facturaRectificativa->precio;
            }
            //dd($facturaRectificativa);
            $this->ingresos_factura = Caja::where('pedido_id', $facturaRectificativa->id)->get();
            $this->facturas_compensadas = FacturasCompensadas::where('factura_id', $facturaRectificativa->id)->get();
            $total = 0;
            foreach ($this->facturas_compensadas as $factura) {
                
                $total += $factura->pagado;
           
            }

            if(count($this->ingresos_factura) > 0){
                $this->importe = $this->importeFactura - $this->ingresos_factura->sum('importe');

            }else{
                $this->importe = $this->importeFactura;
            }

        }

    }
}


    public function crearMovimiento()
    {
        if($this->is_pagado == null){
            $this->is_pagado = false;
        }

        $caja = Caja::create([
            'is_pagado' => $this->is_pagado,
            'banco' => $this->banco,
            'fecha' => $this->fecha,
            'importe' => $this->importe,
            'descripcion' => $this->descripcion,
            'tipo_movimiento' => $this->tipoMovimiento,
            'pedido_id' => $this->pedido_id,
            'poveedor_id' => $this->proveedor_id,
        ]);
        $this->dailyTransactions = $this->getDailyTransactions();
        
        //resetear los campos
        $this->reset('is_pagado', 'banco', 'fecha', 'importe', 'descripcion', 'tipoMovimiento', 'pedido_id' , 'proveedor_id');

        $this->alert('success', 'Movimiento creado correctamente');
        $this->dispatchBrowserEvent('movimientoCreado', ['caja' => $caja]);
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
