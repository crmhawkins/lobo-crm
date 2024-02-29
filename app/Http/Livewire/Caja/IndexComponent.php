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

class IndexComponent extends Component
{
    public $caja;
    public $proceedor;
    public $fechas;
    public $dias;
    public $semana;
    public $saldo_inicial= 0;
    public $saldo_array = [];
    public $clientes;
    public $pedido;
    public $facturas;


    public function mount()
    {
        $this->semana = Carbon::now()->year;
        $this->caja = Caja::all();
        $this->saldo_inicial = Settings::where('id', 1)->first()->saldo_inicial;
        $this->cambioSemana();
        $this->proceedor = Proveedores::all();
        $this->clientes = Clients::all();
        $this->facturas = Facturas::all();


    }
    public function render()
    {
        return view('livewire.caja.index-component');
    }
    public function getCliente($id)
    {
        $id_pedido = $this->facturas->firstWhere('id', $id)->id_pedido;
        $this->pedido = Pedido::find($id_pedido);

         return $this->clientes->firstWhere('id', $this->pedido->cliente_id)->nombre;
    }
    public function calcular_saldo($index, $id)
    {
        $movimiento = $this->caja->where('id', $id)->first();
        if ($index == 0) {
            if ($movimiento->tipo_movimiento == 'Gasto') {
                $this->saldo_array[] = $this->saldo_inicial - $movimiento->importe;
            } else  if ($movimiento->tipo_movimiento == 'Ingreso') {
                $this->saldo_array[] = $this->saldo_inicial + $movimiento->importe;
            }
        } else {
            if ($movimiento->tipo_movimiento == 'Gasto') {
                $this->saldo_array[] = $this->saldo_array[$index - 1] - $movimiento->importe;
            } else if ($movimiento->tipo_movimiento == 'Ingreso') {
                $this->saldo_array[] = $this->saldo_array[$index - 1] + $movimiento->importe;
            }
        }
        return $this->saldo_array[$index];
    }

    public function cambioSemana()
    {

        $fecha = Carbon::now()->setISODate($this->semana, 1, 1);
        $fechaInicio = $fecha->startOfYear()->format('Y-m-d'); // El 1 al final establece el día de inicio de la semana a lunes
        $fechaFin = $fecha->endOfYear()->format('Y-m-d');
        $this->caja = Caja::whereBetween('fecha', [$fechaInicio, $fechaFin])->get();
        $this->saldo_array = [];

    }
    public function proveedorNombre($id)
    {
        return $this->proceedor->find($id)->nombre;
    }
    public function Gasto()
    {
        return redirect()->route('caja.create-gasto');
    }
    public function Ingreso()
    {
        return redirect()->route('caja.create-ingreso');
    }
}
