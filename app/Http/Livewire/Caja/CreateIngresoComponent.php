<?php

namespace App\Http\Livewire\Caja;

use App\Models\Caja;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Facturas;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Bancos;
use App\Models\FacturasCompensadas;
use Illuminate\Support\Facades\DB;
use App\Models\Productos;
use App\Models\StockEntrante;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;
use App\Models\Delegacion;




class CreateIngresoComponent extends Component
{
    use LivewireAlert;

    public $tipo_movimiento = 'Ingreso';
    public $metodo_pago = "giro_bancario";
    public $importe;
    public $descripcion;
    public $pedido_id;
    public $fecha;
    public $clientes;
    public $pedido;
    public $facturas;
    public $bancos;
    public $banco; //banco_id
    public $bancoSeleccionado;
    public $compensacion;
    public $facturaSeleccionada;
    public $importeFactura;
    public $ingresos_factura = [];
    public $compensacion_factura = false;
    public $facturas_compensadas = [];
    public $importeFacturaCompensada;
    public $importeCompensado;
    public $asientoContable;
    public $cuentaContable_id;
    public $cuentasContables;
    public $isIngresoProveedor = false;
    public $gasto_id;
    public $gastos;
     

    public function loadCuentasContables()
    {
        // Similar lógica que tenías en createGasto para estructurar los datos jerárquicos
        $dataSub = [];
        $indice = 0;
        
        $grupos = GrupoContable::orderBy('numero', 'asc')->get();
        foreach ($grupos as $grupo) {
            array_push($dataSub, [
                'grupo' => $grupo,
                'subGrupo' => []
            ]);

            $subGrupos = SubGrupoContable::where('grupo_id', $grupo->id)->get();
            $i = 0;
            foreach ($subGrupos as $subGrupo) {
                array_push($dataSub[$indice]['subGrupo'], [
                    'item' => $subGrupo,
                    'cuentas' => []
                ]);

                $cuentas = CuentasContable::where('sub_grupo_id', $subGrupo->id)->get();
                $index = 0;
                foreach ($cuentas as $cuenta) {
                    array_push($dataSub[$indice]['subGrupo'][$i]['cuentas'], [
                        'item' => $cuenta,
                        'subCuentas' => []
                    ]);

                    $subCuentas = SubCuentaContable::where('cuenta_id', $cuenta->id)->get();

                    if (count($subCuentas) > 0) {
                        $indices = 0;
                        foreach ($subCuentas as $subCuenta) {
                            array_push($dataSub[$indice]['subGrupo'][$i]['cuentas'][$index]['subCuentas'], [
                                'item' => $subCuenta,
                                'subCuentasHija' => []
                            ]);

                            $sub_cuenta = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                            if (count($sub_cuenta) > 0) {
                                foreach ($sub_cuenta as $subCuentaHijo) {
                                    array_push($dataSub[$indice]['subGrupo'][$i]['cuentas'][$index]['subCuentas'][$indices]['subCuentasHija'], $subCuentaHijo);
                                }
                            }
                        }
                    }
                    $index++;
                }
                $i++;
            }
            $indice++;
        }

        $this->cuentasContables = $dataSub;
    }

    public function mount()
    {
        $this->gastos = Caja::where('tipo_movimiento', 'Gasto')->get();
        $this->facturas = Facturas::where(function($query) {
            $query->where('estado', 'Pendiente')
                  ->orWhere('estado', 'Parcial');
        })
        ->whereNull('factura_id')
        ->orderBy('id', 'asc')
        ->get();
        $this->clientes = Clients::all();
        $this->bancos = Bancos::all();
        $this->loadCuentasContables();
        // Obtener el año actual
        $currentYear = date('Y');

        // Contar los asientos contables del año actual
        $cajas = Caja::where('asientoContable', 'like', '%/' . $currentYear)->get();

        // Crear el nuevo asiento contable comenzando desde 0001 si es un nuevo año
        $this->asientoContable = str_pad($cajas->count() + 1, 6, '0', STR_PAD_LEFT) . '/' . $currentYear;
    }


    public function getDelegacion($id)
    {
        $delegaciones = Delegacion::all();
        $cliente = $this->clientes->find($id);
        if (isset($cliente)) {
            return $delegaciones->where('COD', $cliente->delegacion_COD)->first() ? $delegaciones->where('COD', $cliente->delegacion_COD)->first()->nombre : "no definido";
        }
        return "no definido";
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

    public function render()
    {
        if($this->banco){
            $this->bancoSeleccionado = Bancos::find($this->banco);
        }


        return view('livewire.caja.create-ingreso-component');

    }


    public function onFacturaChange($id)
    {
        //dd("hola");
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

    public function submit()
    { 
        //dd($this->isIngresoProveedor);

        //dd($this->tipo_movimiento , $this->metodo_pago , $this->importe , $this->descripcion , $this->pedido_id , $this->fecha , $this->banco , $this->asientoContable , $this->cuentaContable_id , $this->isIngresoProveedor , $this->gasto_id);
        if($this->isIngresoProveedor){
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'tipo_movimiento' => 'required',
                    'metodo_pago' => 'required',
                    'importe' => 'required',
                    'descripcion' => 'required',
                    'fecha' => 'required',
                    'banco' => 'nullable',
                    'gasto_id' => 'required',
                    


                ],
                // Mensajes de error
                [
                    'tipo_movimiento.required' => 'El tipo de movimiento es obligatorio.',
                    'metodo_pago.required' => 'El método de pago es obligatorio.',
                    'importe.required' => 'El importe es obligatorio.',
                    'descripcion.required' => 'La descripción es obligatoria.',
                    'fecha.required' => 'La fecha es obligatoria.',
                ]
            );
        }else{
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'tipo_movimiento' => 'required',
                    'metodo_pago' => 'required',
                    'importe' => 'required',
                    'descripcion' => 'required',
                    'pedido_id' => 'nullable',
                    'fecha' => 'required',
                    'banco' => 'nullable',
                ],
                // Mensajes de error
                [
                    'tipo_movimiento.required' => 'El tipo de movimiento es obligatorio.',
                    'metodo_pago.required' => 'El método de pago es obligatorio.',
                    'importe.required' => 'El importe es obligatorio.',
                    'descripcion.required' => 'La descripción es obligatoria.',
                    'pedido_id.required' => 'El pedido es obligatorio.',
                    'fecha.required' => 'La fecha es obligatoria.',
                ]
            );
        }

        if($this->isIngresoProveedor){
            $usuariosSave = Caja::create([
                'tipo_movimiento' => $this->tipo_movimiento,
                'metodo_pago' => $this->metodo_pago,
                'importe' =>  $this->compensacion_factura ? $this->importeFacturaCompensada :  $this->importe,
                'descripcion' => $this->descripcion,
                'gasto_id' => $this->gasto_id,
                'fecha' => $this->fecha,
                'banco' => $this->banco,
                'asientoContable' => $this->asientoContable,
                'cuentaContable_id' => $this->cuentaContable_id,
                'isIngresoProveedor' => $this->isIngresoProveedor
            ]);
        }else{
            $usuariosSave = Caja::create([
                'tipo_movimiento' => $this->tipo_movimiento,
                'metodo_pago' => $this->metodo_pago,
                'importe' =>  $this->compensacion_factura ? $this->importeFacturaCompensada :  $this->importe,
                'descripcion' => $this->descripcion,
                'pedido_id' => $this->pedido_id,
                'fecha' => $this->fecha,
                'banco' => $this->banco,
                'asientoContable' => $this->asientoContable,
                'cuentaContable_id' => $this->cuentaContable_id,
                'isIngresoProveedor' => $this->isIngresoProveedor
            ]);
        }
        
        event(new \App\Events\LogEvent(Auth::user(), 52, $usuariosSave->id));

        if(!$this->isIngresoProveedor){

            //$this->importeFactura = $this->facturaSeleccionada->total;
            $this->ingresos_factura = Caja::where('pedido_id', $this->facturaSeleccionada->id)->get();
            $importe = $this->importeFactura;
            if(count($this->ingresos_factura) > 0){
                $importe = $this->importeFactura - $this->ingresos_factura->sum('importe');

            }

            if($importe - $this->importeCompensado <= 0 ){
                
                $this->facturaSeleccionada->estado = 'Pagado';



                $this->facturaSeleccionada->save();
            }else{
                $this->facturaSeleccionada->estado = 'Parcial';
                $this->facturaSeleccionada->save();
            }

        }

        


        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', '¡Movimiento registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del movimiento!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'onFacturaChange'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('caja.index');
    }

    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
    }
}
