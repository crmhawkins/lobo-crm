<?php

namespace App\Http\Livewire\Caja;

use App\Models\Clients;
use App\Models\Proveedores;
use App\Models\Facturas;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Caja;;
use Illuminate\Support\Facades\Auth;
use App\Models\Delegacion;
use Livewire\WithFileUploads;
use App\Models\FacturasCompensadas;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;


class CreateGastoComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $tipo_movimiento = 'Gasto';
    public $metodo_pago;
    public $importe;
    public $descripcion;
    public $fecha;
    public $clientes;
    public $poveedor_id;
    public $poveedores;
    public $facturas;
    public $banco;
    public $estado ='Pendiente';
    public $delegaciones = [];
    public $delegacion_id;
    public $departamento;
    public $iva = 0;
    public $descuento = 0;
    public $retencion = 0;
    public $importe_neto;
    public $fecha_vencimiento;
    public $fecha_pago;
    public $cuenta;
    public $importeIva;
    public $total;
    public $documento;
    public $documentoPath;
    public $nInterno;
    public $nFactura;
    public $pagado;
    public $pendiente;
    public $compensacion = false;
    public $factura_id;
    public $asientoContable;
    public $cuentaContable_id;
    public $cuentasContables;
    public $facturasSeleccionadas = [];
    public $pagos = [];
    
    
    public function mount()
    {
        $this->poveedores = Proveedores::all();
        $this->clientes = Clients::all();
        $this->delegaciones = Delegacion::all();

        //generar numero interno de esta manera: 06(nombremesactual)_000(Siguiente numero de la base de datos)
        $this->nInterno = date('m').'_'.str_pad(Caja::where('tipo_movimiento', 'Gasto')->count() + 1, 3, '0', STR_PAD_LEFT);
        

        $this->facturas = Facturas::where('estado', 'Pendiente')
        ->orWhere('estado', 'Parcial')
        ->get();

        $this->loadCuentasContables();

        $currentYear = date('Y');

        // Contar los asientos contables del año actual
        $cajas = Caja::where('asientoContable', 'like', '%/' . $currentYear)->get();

        // Crear el nuevo asiento contable comenzando desde 0001 si es un nuevo año
        $this->asientoContable = str_pad($cajas->count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $currentYear;


    }

    public function guardarFacturasCompensadas()
    {
        // Validar que se hayan seleccionado facturas
        $this->validate([
            'facturasSeleccionadas' => 'required|array|min:1',
            'pagos.*' => 'required|numeric|min:0',
        ]);
    
        // Calcular el total de los pagos
        $totalPagadoCompensadas = array_sum($this->pagos);
    
        // Actualizar el valor del campo 'pagado' con la suma
        $this->pagado = $totalPagadoCompensadas;
        $this->pendiente = $this->total - $this->pagado;

        
    
        // Alerta de éxito al guardar las facturas compensadas
        $this->alert('success', '¡Facturas seleccionadas para compensar y total pagado actualizado!');
    }

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

    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
    }
    public function render()
    {
        return view('livewire.caja.create-gasto-component');
    }

    public function updating($property , $value){
        //dd($property, $value);
        if($property === 'poveedor_id' && $value !== null && $value !== '0'){
            $proveedor = Proveedores::find($value);
            $this->cuenta = $proveedor->cuenta_contable;
        }
    }
    // public function save(){

    //     //validate pdf
    //     $this->validate([
    //         'documento' => 'required|mimes:pdf|max:1024',
    //     ]);

    //     //$this->documento->store('documentos_gastos', );
    //     $this->documento->storeAs('documentos_gastos', $this->documento->hashName() , 'private');
    //     //documentpath es la ruta donde se guarda el archivo
    //     $this->documentoPath = $this->documento->hashName();
    //     dd( $this->documentoPath);
    // }

    public function calcularTotal(){
        if($this->importe !== null && $this->importe !== ''){
            if($this->iva === null || $this->iva === ''){
                    $this->iva = 0;
            }
            if($this->retencion === null || $this->retencion === ''){
                $this->retencion = 0;
            }
            if($this->descuento === null || $this->descuento === ''){
                $this->descuento = 0;
            }

            $this->importeIva = $this->importe * $this->iva / 100;
            
            $retencionTotal = $this->importe * $this->retencion / 100;
            $this->total = $this->importe + $this->importeIva - $retencionTotal;
            if($this->descuento !== null){
                $this->total = round($this->total - ($this->total * $this->descuento / 100) , 2);   
            }
        }

    }

    public function updated($property, $value){
        if($property === 'pagado' || $property === 'total' ){
            if($this->pagado !== null && $this->total !== null && is_numeric($this->pagado) && is_numeric($this->total)){
                $this->pendiente = $this->total - $this->pagado;
            }
        }
    }

    public function submit()
    {

        //dd con todos los campos del validate
        //dd($this->tipo_movimiento, $this->metodo_pago, $this->importe, $this->descripcion, $this->poveedor_id, $this->fecha, $this->estado, $this->banco, $this->delegacion_id, $this->departamento, $this->iva, $this->retencion, $this->importe_neto, $this->fecha_vencimiento, $this->fecha_pago, $this->cuenta, $this->importeIva, $this->total, $this->documento, $this->documentoPath, $this->nInterno, $this->nFactura, $this->pagado, $this->pendiente, $this->facturas, $this->pagos);

        // Validación de datos
        $validatedData = $this->validate(
            [
                'tipo_movimiento' => 'required',
                'metodo_pago' => 'required',
                'importe' => 'required',
                'descripcion' => 'required',
                'poveedor_id' => 'nullable',
                'fecha' => 'required',
                'estado' => 'nullable',
                'banco' => 'nullable',
                'delegacion_id' => 'nullable',
                'departamento' => 'nullable',
                'iva' => 'nullable',
                'descuento' => 'nullable',
                'retencion' => 'nullable',
                'importe_neto' => 'nullable',
                'fecha_vencimiento' => 'nullable',
                'fecha_pago' => 'nullable',
                'cuenta' => 'nullable',
                'importeIva' => 'nullable',
                'total' => 'nullable',
                'pagado' => 'nullable',
                'pendiente' => 'nullable',
                //documento maximo 1gb
                'documento' => 'required|mimes:pdf|max:1048576',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                //documento max
                'documento.max' => 'El documento no puede pesar más de 1GB.',

                
            ]
        );

        $this->documento->storeAs('documentos_gastos', $this->documento->hashName() , 'private');
        $this->documentoPath = $this->documento->hashName();

        // Guardar datos validados
        $usuariosSave = Caja::create([
            'tipo_movimiento' => $this->tipo_movimiento,
            'metodo_pago' => $this->metodo_pago,
            'importe' => $this->importe,
            'descripcion' => $this->descripcion,
            'poveedor_id' => $this->poveedor_id,
            'fecha' => $this->fecha,
            'banco' => $this->banco,
            'delegacion_id' => $this->delegacion_id,
            'departamento' => $this->departamento,
            'iva' => $this->iva,
            'descuento' => $this->descuento,
            'retencion' => $this->retencion,
            'importe_neto' => $this->importe_neto,
            'fechaVencimiento' => $this->fecha_vencimiento,
            'fechaPago' => $this->fecha_pago,
            'cuenta' => $this->cuenta,
            'importeIva' => $this->importeIva,
            'total' => $this->total,
            'documento_pdf' => $this->documentoPath,
            'estado' => $this->estado,
            'nFactura' => $this->nFactura,
            'nInterno' => $this->nInterno,
            'pagado' => $this->pagado,
            'pendiente' => $this->pendiente,
            'asientoContable' => $this->asientoContable,
            'cuentaContable_id' => $this->cuentaContable_id,
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 52, $usuariosSave->id));

        // Alertas de guardado exitoso
        if ($usuariosSave) {

            // if($this->compensacion){
            //     $factura = Facturas::find($this->factura_id);
            //     $facturaCompensada = FacturasCompensadas::create([
            //         'caja_id' => $usuariosSave->id,
            //         'factura_id' => $factura->id,
            //         'importe' => $factura->total,
            //         'pagado' => $this->pagado != null && $this->pagado > 0 ? $this->pagado : $this->total,
            //         'pendiente' => $factura->total - ($this->pagado != null && $this->pagado > 0 ? $this->pagado : $this->total),
            //         'fecha' => $this->fecha,
            //     ]);
                
            // }

            // Guardar facturas compensadas si existen facturas seleccionadas
            if ($this->compensacion && !empty($this->facturasSeleccionadas)) {
                foreach ($this->facturasSeleccionadas as $index => $factura_id) {
                    $factura = Facturas::find($factura_id);

                    FacturasCompensadas::create([
                        'caja_id' => $usuariosSave->id,
                        'factura_id' => $factura->id,
                        'importe' => $factura->total,
                        'pagado' => $this->pagos[$index] ?? $factura->total,
                        'pendiente' => $factura->total - ($this->pagos[$index] ?? $factura->total),
                        'fecha' => $this->fecha,
                    ]);
                }
            }


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
            'submit'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('caja.index');
    }


}
