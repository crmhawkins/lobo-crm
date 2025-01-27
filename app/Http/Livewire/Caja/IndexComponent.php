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

class IndexComponent extends Component
{
    use LivewireAlert, WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $caja;
    public $proveedores;
    public $fechas;
    public $dias;
    public $mes;
    public $ano;
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
    public $proveedorId;
    
    public $filtro;
    public $filtroEstado;
    public $page = 1; // Variable para almacenar la página actual

    public $itemsPerPage = 0; // Por defecto sin paginación

    protected $listeners = ['setPage'];

    public function descargarTodosDocumentos()
    {
        $this->cambioMes();
        $caja = $this->caja->where('documento_pdf', '!=', null)
                        ->where('tipo_movimiento', 'Gasto');
                        
        if($this->proveedorId != null){
            $caja = $caja->where('poveedor_id', $this->proveedorId);
        }

       // dd($caja);
        
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
                return response()->json(['error' => 'No se pudo crear el archivo ZIP' ], 500);
            }
        }catch(\Exception $e){
            
            //livewire alert error
            $this->alert('error', '¡No se ha podido descargar los documentos!' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);

        }
            
    }

    public function updatedPage($value)
{
    // Actualiza la URL cuando cambia la página
    $this->emit('updateUrl', ['page' => $value]);
}

public function setPage($page)
{
    $this->page = $page;
    $this->cambioMes(); // Vuelve a cargar los datos para la nueva página
}

    public function mount()
{
    $this->mes = session('caja_filtro_mes', Carbon::now()->format('n')); // 'n' devuelve el mes como número sin ceros iniciales
    if (empty($this->mes)) {
        $this->mes = Carbon::now()->format('n'); // Establece el mes actual si $this->mes es null o vacío
    }
    $this->ano = session('caja_filtro_ano', Carbon::now()->format('Y'));
   // dd($this->mes);
    $this->filtro = session('caja_filtro', null);
    $this->filtroEstado = session('caja_filtro_estado', null);
    $this->delegacion = session('caja_filtro_delegacion', null);
    $this->fechaPago = session('caja_filtro_fecha_pago', null);
    $this->fechaVencimiento = session('caja_filtro_fecha_vencimiento', null);
    $this->fecha = session('caja_filtro_fecha', null);
    $this->proveedorId = session('caja_filtro_proveedor_id', null);

    $this->caja = Caja::orderBy('fecha')->paginate(10);
    $this->saldo_inicial = Settings::where('id', 1)->first()->saldo_inicial;
    $this->cambioMes();
    $this->proveedores = Proveedores::all();
    $this->clientes = Clients::all();
    $this->facturas = Facturas::all();
    $this->delegaciones = Delegacion::all();
}


   

    public function updated($property, $value){
        if($property == 'filtro' || $property == 'filtroEstado' || $property == 'delegacion'
        || $property == 'fechaPago' || $property == 'fechaVencimiento' || $property == 'fecha' || $property == 'proveedorId'){
            $this->cambioMes();
        }   
    }


    public function render()
    {
        return view('livewire.caja.index-component', [
            'caja' => $this->caja,
        ]);
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
        $compensacion = FacturasCompensadas::where('caja_id', $id)->sum('pagado');
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
                    if($compensacion > 0){
                        $this->saldo_array[$index] = $this->saldo_inicial - $movimiento->total + $compensacion;
                    }else{
                        $this->saldo_array[$index] = $this->saldo_inicial - $movimiento->total;
                    }

                } elseif ($movimiento->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_inicial + $movimiento->importe;
                }
            }else{
                if ($movimientoPendiente->tipo_movimiento == 'Gasto') {
                    if($compensacion > 0){
                        $this->saldo_array[$index] = $this->saldo_inicial - $movimientoPendiente->pagado + $compensacion;
                    }else{
                        $this->saldo_array[$index] = $this->saldo_inicial - $movimientoPendiente->pagado;
                    }
                } elseif ($movimientoPendiente->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_inicial + $movimientoPendiente->importe;
                }
            }
           
        } else {

            if($movimiento != null){
                if ($movimiento->tipo_movimiento == 'Gasto') {
                    if($compensacion > 0){
                        $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimiento->total + $compensacion;
                    }else{
                        $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimiento->total;
                    }
                } elseif ($movimiento->tipo_movimiento == 'Ingreso') {
                    $this->saldo_array[$index] = $this->saldo_array[$index - 1] + $movimiento->importe;
                }
            }else{
                if ($movimientoPendiente->tipo_movimiento == 'Gasto') {
                    if($compensacion > 0){
                        $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimientoPendiente->pagado + $compensacion;
                    }else{
                        $this->saldo_array[$index] = $this->saldo_array[$index - 1] - $movimientoPendiente->pagado;
                    }
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
        
        foreach($this->caja as $c){
            if($c->tipo_movimiento == 'Gasto'){
                $compensacion = FacturasCompensadas::where('caja_id', $c->id)->sum('pagado');
                $this->gastos -= $compensacion;
            }else{
                $compensacion = FacturasCompensadas::where('factura_id', $c->id)->sum('pagado');
                $this->ingresos += $compensacion;
            }  
        }

        $this->gastos += $pendientesPagado;
    }

    public function getCompensacion($id, $tipo)
    {
        if($tipo == 'Gasto'){
            $ingreso = $this->caja->firstWhere('id', $id);
            if($ingreso == null){
                return 0;
            }
            $compensacion = FacturasCompensadas::where('caja_id', $ingreso->id)->sum('pagado');
            return $compensacion;
        }else{
            $factura = $this->facturas->firstWhere('id', $id);
            if($factura == null){
                return 0;
            }
            $compensacion = FacturasCompensadas::where('factura_id', $factura->id)->sum('pagado');
            return $compensacion;
        }
       
    }

    public function cambioMes()
    {
        // Guardar los filtros en la sesión
        session([
            'caja_filtro' => $this->filtro,
            'caja_filtro_estado' => $this->filtroEstado,
            'caja_filtro_delegacion' => $this->delegacion,
            'caja_filtro_fecha_pago' => $this->fechaPago,
            'caja_filtro_fecha_vencimiento' => $this->fechaVencimiento,
            'caja_filtro_fecha' => $this->fecha,
            'caja_filtro_proveedor_id' => $this->proveedorId,
            'caja_filtro_mes' => $this->mes,
            'caja_filtro_ano' => $this->ano,
        ]);

        // Si $this->mes es null o vacío, mostrar todo el año
        if (empty($this->mes)) {
            $fechaInicio = Carbon::createFromDate($this->ano, 1, 1)->startOfYear();
            $fechaFin = Carbon::createFromDate($this->ano, 12, 31)->endOfYear();
        } else {
            $mesNumero = is_numeric($this->mes) ? $this->mes : Carbon::parse($this->mes)->month;
            $fechaInicio = Carbon::createFromDate($this->ano, $mesNumero, 1)->startOfMonth();
            $fechaFin = Carbon::createFromDate($this->ano, $mesNumero, 1)->endOfMonth();
        }

        $query = Caja::whereBetween('fecha', [$fechaInicio, $fechaFin])
                     ->orderBy('fecha');

        if ($this->filtro != 'Todos' && $this->filtro != null) {
            $query->where('tipo_movimiento', $this->filtro);
        }

        if ($this->filtroEstado != 'Todos' && $this->filtroEstado != null) {
            $query->where('estado', $this->filtroEstado);
        }

        if ($this->delegacion != 'Todos' && $this->delegacion != null) {
            $query->where('delegacion_id', $this->delegacion);
        }

        if ($this->fechaPago != null) {
            $query->where('fechaPago', $this->fechaPago);
        }

        if ($this->fechaVencimiento != null) {
            $query->where('fechaVencimiento', $this->fechaVencimiento);
        }

        if ($this->fecha != null) {
            $query->where('fecha', $this->fecha);
        }

        if ($this->proveedorId != null && $this->proveedorId != 'Todos') {
            $query->where('poveedor_id', $this->proveedorId);
        }

        // Modificar la consulta de paginación
        if ($this->itemsPerPage > 0) {
            $this->caja = $query->paginate($this->itemsPerPage, ['*'], 'page', $this->page);
        } else {
            $this->caja = $query->get(); // Obtener todos los elementos si itemsPerPage es 0 o negativo
        }

        $this->calcularIngresoyGasto();
        $this->saldo_array = [];
    }

    public function proveedorNombre($id)
    {
        return $this->proveedores->find($id)->nombre;
    }
    public function Gasto()
    {
        return redirect()->route('caja.create-gasto');
    }
    public function Ingreso()
    {
        return redirect()->route('caja.create-ingreso');
    }

    public function updatedMes($value)
    {
        // Si el valor es un string en formato 'YYYY-MM', extrae solo el mes
        if (preg_match('/^\d{4}-(\d{2})$/', $value, $matches)) {
            $this->mes = (int)$matches[1]; // Convierte a número
        } else {
            $this->mes = (int)$value; // Asegúrate de que sea un número
        }
        $this->cambioMes();
    }
}
