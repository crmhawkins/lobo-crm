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
    public $mes;
    public $saldo_inicial= 0;
    public $saldo_array = [];
    public $clientes;
    public $pedido;
    public $facturas;

    public $filtro;



    public function mount()
    {
        $this->mes = Carbon::now()->format('Y-m'); // Año-mes actual
        $this->caja = Caja::orderBy('fecha')->get();
        $this->saldo_inicial = Settings::where('id', 1)->first()->saldo_inicial;
        $this->cambioMes();
        $this->proceedor = Proveedores::all();
        $this->clientes = Clients::all();
        $this->facturas = Facturas::all();


    }

    public function updated($property, $value){
        if($property == 'filtro'){
            $this->cambioMes();
        }   
    }


    public function render()
    {
        return view('livewire.caja.index-component');
    }
    public function getFactura($id)
    {
        $factura = $this->facturas->firstWhere('id', $id);

        if( isset( $factura)){

        $cliente = $this->clientes->firstWhere('id',  $factura->cliente_id)->nombre;


        return $factura ->numero_factura . "-" . $cliente ;

        }else{ return "Factura no encontrada";}
    }
    public function calcular_saldo($index, $id)
    {
        $movimiento = $this->caja->where('id', $id)->first();
        //dd($movimiento);
        if ($index == 0) {
            
            if ($movimiento->tipo_movimiento == 'Gasto') {
                $this->saldo_array[$index] = $this->saldo_inicial - $movimiento->total;
            } elseif ($movimiento->tipo_movimiento == 'Ingreso') {
                $this->saldo_array[$index] = $this->saldo_inicial + $movimiento->importe;
            }
        } else {
            if ($movimiento->tipo_movimiento == 'Gasto') {
                $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimiento->total;
            } elseif ($movimiento->tipo_movimiento == 'Ingreso') {
                $this->saldo_array[$index] = $this->saldo_array[$index - 1] + $movimiento->importe;
            }
        }
        return $this->saldo_array[$index];
    }

    public function cambioMes()
    {
        list($year, $month) = explode('-', $this->mes);
        $fechaInicio  = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $fechaFin = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Formato de las fechas para comparación en la base de datos
        $fechaInicio = $fechaInicio->format('Y-m-d');
        $fechaFin = $fechaFin->format('Y-m-d');

        // Obtener registros de la tabla Caja que están entre fechaInicio y fechaFin
        $this->caja = Caja::whereBetween('fecha', [$fechaInicio, $fechaFin])->orderBy('fecha')->get();

        //si filtro es diferente de todos
        if($this->filtro != 'Todos' && $this->filtro != null){
            $this->caja = $this->caja->where('tipo_movimiento', $this->filtro);
        }

        // Reiniciar saldo_array
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
