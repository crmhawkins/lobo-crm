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

class IndexComponent extends Component
{
    use LivewireAlert;

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

    public $ingresos;
    public $gastos;
    public $delegaciones;
    public $delegacion;
    public $fechaPago;
    public $fechaVencimiento;
    public $fecha;
    
    


    public $filtro;
    public $filtroEstado;

    public function descargarTodosDocumentos()
    {
        $caja = $this->caja->where('documento_pdf', '!=', null)
                        ->where('tipo_movimiento', 'Gasto');
        
        $zip = new \ZipArchive;
        
        //dd($this->mes);
       
        $mesActual = Carbon::parse($this->mes)->format('F');
        //en español
        $meses = array(
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        );
        $mesActual = $meses[$mesActual];

        $ano = Carbon::parse($this->mes)->format('Y');

        $zipFileName = 'documentos_gastos_' . $mesActual . '_' . $ano . '.zip';
        $zipFilePath = storage_path($zipFileName);
        try{
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                foreach ($caja as $c) {
                    $proveedor = Proveedores::find($c->poveedor_id);
                    $proveedor_name = $proveedor ? $proveedor->nombre : 'desconocido';
                    $documento = $c->documento_pdf;
                    $documentPath = storage_path('app/private/documentos_gastos/' . $documento);
                    
                    if (file_exists($documentPath)) {
                        $nombrePersonalizado = $c->nInterno . '_' . $proveedor_name . '_' . $c->fecha . '.pdf';
                        $zip->addFile($documentPath, $nombrePersonalizado);
                    }
                }
                $zip->close();
                
                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
            }
        }catch(\Exception $e){
            
            //livewire alert error
            $this->alert('error', '¡No se ha podido descargar los documentos!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);

        }
            
    }

    public function mount()
    {
        $this->mes = Carbon::now()->format('Y-m'); // Año-mes actual
        $this->caja = Caja::orderBy('fecha')->get();
        $this->saldo_inicial = Settings::where('id', 1)->first()->saldo_inicial;
        $this->cambioMes();
        $this->proceedor = Proveedores::all();
        $this->clientes = Clients::all();
        $this->facturas = Facturas::all();
        $this->delegaciones = Delegacion::all();


    }

    public function updated($property, $value){
        if($property == 'filtro' || $property == 'filtroEstado' || $property == 'delegacion'
        || $property == 'fechaPago' || $property == 'fechaVencimiento' || $property == 'fecha'){
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
        $movimiento = $this->caja->where('id', $id)->where('estado', '!=','Pendiente')->first();
        $movimientoPendiente = $this->caja->where('id', $id)->where('estado', 'Pendiente')->first();
        //dd($movimiento);

        if ($movimiento == null && $movimientoPendiente == null) {
            if($index == 0)
            {
                return $this->saldo_array[$index] = isset($this->saldo_inicial) ? $this->saldo_inicial : 0;
            }
            return $this->saldo_array[$index] = $this->saldo_array[$index - 1];
        }

        if ($index == 0) {
            if($movimiento != null){
                if ($movimiento->tipo_movimiento == 'Gasto') {
                    $this->saldo_array[$index] = $this->saldo_inicial - $movimiento->total;
                } elseif ($movimiento->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_inicial + $movimiento->importe;
                }
            }else{
                if ($movimientoPendiente->tipo_movimiento == 'Gasto') {
                    $this->saldo_array[$index] = $this->saldo_inicial - $movimientoPendiente->pagado;
                } elseif ($movimientoPendiente->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_inicial + $movimientoPendiente->importe;
                }
            }
           
        } else {

            if($movimiento != null){
                if ($movimiento->tipo_movimiento == 'Gasto') {
                    $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimiento->total;
                } elseif ($movimiento->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_array[$index - 1] + $movimiento->importe;
                }
            }else{
                if ($movimientoPendiente->tipo_movimiento == 'Gasto') {
                    $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimientoPendiente->pagado;
                } elseif ($movimientoPendiente->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_array[$index - 1] + $movimientoPendiente->importe;
                }
            }

        }
        return $this->saldo_array[$index];
    }

    public function calcularIngresoyGasto(){
        $this->ingresos = $this->caja->where('tipo_movimiento', 'Ingreso')->sum('importe');
        $this->gastos = $this->caja->where('tipo_movimiento', 'Gasto')->where('estado', '!=', 'Pendiente')->sum('total');
        $pendientesPagado = $this->caja->where('estado', 'Pendiente')->sum('pagado');
        $this->gastos += $pendientesPagado;
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

        if($this->filtroEstado != 'Todos' && $this->filtroEstado != null){
            $this->caja = $this->caja->where('estado', $this->filtroEstado);
        }

        if($this->delegacion != 'Todos' && $this->delegacion != null){
            $this->caja = $this->caja->where('delegacion_id', $this->delegacion);
        }

        if($this->fechaPago != null){
            $this->caja = $this->caja->where('fechaPago', $this->fechaPago);
        }

        if($this->fechaVencimiento != null){
            $this->caja = $this->caja->where('fechaVencimiento', $this->fechaVencimiento);
        }

        if($this->fecha != null){
            $this->caja = $this->caja->where('fecha', $this->fecha);
        }

        $this->calcularIngresoyGasto();

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
