<?php

namespace App\Http\Livewire\Caja;

use App\Models\Caja;
use App\Models\Pedido;
use App\Models\Proveedores;
use App\Models\Clients;
use App\Models\Facturas;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Delegacion;
use Livewire\WithFileUploads;
use App\Models\FacturasCompensadas;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;




class EditComponent extends Component
{
	use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $tipo_movimiento;
    public $metodo_pago;
    public $importe;
    public $descripcion;
    public $poveedores;
    public $poveedor_id;
    public $fecha;
    public $clientes;
    public $categorias;
    public $pedido_id;
    public $pedido;
    public $facturas;
    public $estado;
    public $banco;
    public $delegaciones = [];
    public $delegacion_id;
    public $departamento;
    public $iva;
    public $descuento;
    public $retencion;
    public $importe_neto;
    public $fecha_vencimiento;
    public $fecha_pago;
    public $cuenta;
    public $importeIva;
    public $total;
    public $documento;
    public $documentoSubido;
    public $documentoPath;
    public $nInterno;
    public $nFactura;
    public $pagado;
    public $pendiente;
    public $facturas_compensadas = [];
    public $compensacion = false;
    public $factura_id;
    public $asientoContable;
    public $cuentaContable_id;
    public $cuentasContables;
    public $facturasSeleccionadas = [];
    public $pagos = [];
    public $isIngresoProveedor = false;
    public $gasto_id;
    public $gastos = [];
    public $selectedGasto;
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
        $caja = Caja::find($this->identificador);
        $this->poveedores = Proveedores::all();
        $this->facturas = Facturas::all();
        $this->clientes = Clients::all();
        $this->metodo_pago = $caja->metodo_pago;
        $this->descripcion = $caja->descripcion;
        $this->importe = $caja->importe;
        $this->poveedor_id = $caja->poveedor_id;
        $this->pedido_id = $caja->pedido_id;
        $this->fecha = $caja->fecha;
        $this->estado = $caja->estado;
        $this->tipo_movimiento = $caja->tipo_movimiento;
        if($this->tipo_movimiento === 'Gasto'){
            $this->facturas_compensadas = FacturasCompensadas::where('caja_id', $this->identificador)->get();
            $this->facturasSeleccionadas = FacturasCompensadas::where('caja_id', $this->identificador)
            ->pluck('factura_id')->toArray();
            // Cargar los importes pagados en el array de pagos
            foreach($this->facturas_compensadas as $facturaCompensada) {
                $this->pagos[] = $facturaCompensada->pagado;
            }
            if($this->facturas_compensadas->count() > 0){
                $this->compensacion = true;
                $this->factura_id =  $this->facturas_compensadas->first()->factura_id;
            }
        }else{
            $this->facturas_compensadas = FacturasCompensadas::where('factura_id', $this->pedido_id)->get();
            $this->isIngresoProveedor = $caja->isIngresoProveedor;
            if($this->isIngresoProveedor){
                $this->gasto_id = $caja->gasto_id;
                $this->selectedGasto = $caja->gasto_id;
                //dd($this->gasto_id);
                $this->gastos = Caja::where('id', $this->gasto_id)->first()->gastos;
            }
        }
        $this->banco = $caja->banco;
        $this->delegacion_id = $caja->delegacion_id;
        $this->departamento = $caja->departamento;
        $this->iva = $caja->iva;
        $this->descuento = $caja->descuento;
        $this->retencion = $caja->retencion;
        $this->importe_neto = $caja->importe_neto;
        $this->fecha_vencimiento = $caja->fechaVencimiento;
        $this->fecha_pago = $caja->fechaPago;
        $this->documento = $caja->documento_pdf;
        $this->nInterno = $caja->nInterno;
        $this->nFactura = $caja->nFactura;
        
        $this->cuenta = $caja->cuenta;
        $this->delegaciones = Delegacion::all();
        $this->importeIva = $caja->importeIva;
        $this->total = $caja->total;
        $this->pagado = $caja->pagado;
        $this->pendiente = $caja->pendiente;

        //asiento contable debe ser 0001/2024 donde 0001 es el numero que sigue en la bbdd y el 2024 es el año actual
        $this->asientoContable = $caja->asientoContable;

        if ($this->asientoContable === null) {
            // Obtener el año actual
            $currentYear = date('Y');

            // Contar los asientos contables del año actual
            $cajas = Caja::where('asientoContable', 'like', '%/' . $currentYear)->get();

            // Crear el nuevo asiento contable comenzando desde 0001 si es un nuevo año
            $this->asientoContable = str_pad($cajas->count() + 1, 4, '0', STR_PAD_LEFT) . '/' . $currentYear;
        }
        $this->cuentaContable_id = $caja->cuentaContable_id;
        $this->loadCuentasContables();

    }
    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
    }
    public function getDelegacion($id)
    {
        $delegaciones = Delegacion::all();
        $cliente = $this->clientes->find($id);
        if (isset($cliente)) {
            return $delegaciones->where('COD', $cliente->delegacion_COD)->first()->nombre;
        }
        return "no definido";
    }

    public function facturaHasIva($id)
    {
        $factura = $this->facturas->firstWhere('id', $id);

        //dependiendo de que delegacion sea el cliente se le aplica iva o no
        $delegacion = $this->getDelegacion($factura->cliente_id);
        if($delegacion == '07 CANARIAS' || $delegacion == '13 GIBRALTAR' || $delegacion == '14 CEUTA' || $delegacion == '15 MELILLA' || $delegacion == '01.1 ESTE – SUR EXTERIOR' || $delegacion == '08 OESTE - INSULAR'){
            return false;
        }else{
            return true;
        }
    }


    public function descargarDocumento()
    {
        if($this->documento === null || $this->documento === ''){
            return;
        }

        $proveedor_name = Proveedores::find($this->poveedor_id)->nombre;

        return response()->download(storage_path('app/private/documentos_gastos/' . $this->documento),
        $this->nInterno.'_'.$proveedor_name.'_'.$this->fecha.'.pdf'
    );
    }

    public function getFacturaNumber($id)
    {
        $factura = $this->facturas->firstWhere('id', $id);
        if(isset($factura)){
           $nombre = $factura->numero_factura;
            return  $nombre ;
        }else{
            return "Factura no encontrada";
        }
    }
    public function guardarFacturasCompensadas()
{
    // Validar que los pagos sean válidos solo si hay facturas seleccionadas
    $this->validate([
        'pagos.*' => 'nullable|numeric|min:0',
    ]);

    // Obtener las facturas compensadas actuales
    $facturasCompensadasActuales = FacturasCompensadas::where('caja_id', $this->identificador)
                                ->pluck('factura_id')
                                ->toArray();

    // Facturas que fueron eliminadas por el usuario
    $facturasEliminadas = array_diff($facturasCompensadasActuales, $this->facturasSeleccionadas);

    // Eliminar las facturas compensadas que ya no estén seleccionadas
    FacturasCompensadas::whereIn('factura_id', $facturasEliminadas)
                        ->where('caja_id', $this->identificador)
                        ->delete();

    // Si el usuario ha quitado todas las facturas, asegurarse de que no queden registros
    if (empty($this->facturasSeleccionadas)) {
        // Establecer los valores de pagado y pendiente a 0
        $this->pagado = 0;
        $this->pendiente = $this->total;
    } else {
        // Actualizar o agregar pagos para cada factura seleccionada
        foreach ($this->facturasSeleccionadas as $index => $factura_id) {
            $factura = Facturas::find($factura_id);

            // Si el pago no existe en el array, lo inicializamos con el total de la factura
            if (!isset($this->pagos[$index])) {
                $this->pagos[$index] = $factura->total;
            }

            // Verificar si la factura compensada ya existe
            $facturaCompensada = FacturasCompensadas::where('caja_id', $this->identificador)
                                    ->where('factura_id', $factura_id)
                                    ->first();

            if ($facturaCompensada) {
                // Actualizar si ya existe
                $facturaCompensada->update([
                    'importe' => $factura->total,
                    'pagado' => $this->pagos[$index],
                    'pendiente' => $factura->total - $this->pagos[$index],
                    'fecha' => now(),
                ]);
            } else {
                // Crear una nueva entrada de factura compensada
                FacturasCompensadas::create([
                    'caja_id' => $this->identificador,
                    'factura_id' => $factura_id,
                    'importe' => $factura->total,
                    'pagado' => $this->pagos[$index],
                    'pendiente' => $factura->total - $this->pagos[$index],
                    'fecha' => now(),
                ]);
            }
        }

        // Actualizar el pagado total de la caja
        $this->pagado = array_sum($this->pagos);
        $this->pendiente = $this->total - $this->pagado;
    }

    // Mostrar alerta de éxito
    $this->alert('success', '¡Facturas compensadas guardadas correctamente!');
}



    public function updating($property , $value){
        //dd($property, $value);
        if($property === 'poveedor_id' && $value !== null && $value !== '0'){
            $proveedor = Proveedores::find($value);
            $this->cuenta = $proveedor->cuenta_contable;
        }
    }

    public function updated($property){
        if($property === 'pagado' || $property === 'total'){

            if($this->pagado !== null && $this->total !== null && is_numeric($this->pagado) && is_numeric($this->total)){
                $this->pendiente = $this->total - $this->pagado;

            }
           
        }
    }

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

            if($this->facturaHasIva($this->$factura->id)){
                $this->importeIva = $this->importe * $this->iva / 100;
            }else{
                $this->importeIva = 0;
            }

            
            $retencionTotal = $this->importe * $this->retencion / 100;
            $this->total = $this->importe + $this->importeIva + $retencionTotal;
            if($this->descuento !== null){
                $this->total = round($this->total - ($this->total * $this->descuento / 100) , 2);   
            }
        }

    }

   

    public function render()
    {
        return view('livewire.caja.edit-component');
    }

// Al hacer update en el formulario
    public function update()
    {

        

        // Validación de datos
        $this->validate([
            'metodo_pago' => 'required',
            'importe' => 'required',
            'poveedor_id' => 'nullable',
            'pedido_id' => 'nullable',
            'fecha' => 'required',
            'tipo_movimiento' => 'required',
            'descripcion' => 'required',
            'estado' => 'nullable',
            


        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

            if($this->documentoSubido !== null){
                
                $this->documentoSubido->storeAs('documentos_gastos', $this->documentoSubido->hashName() , 'private');
                $this->documentoPath = $this->documentoSubido->hashName();
                //eliminar el documento anterior cuyo nombre es $documento
                $caja = Caja::find($this->identificador);
                $documentoAnterior = $caja->documento;
                if($documentoAnterior !== null){
                    unlink(storage_path('app/private/documentos_gastos/' . $documentoAnterior));
                }

            }else{
                $this->documentoPath = $this->documento;
            }

        // Encuentra el identificador
        $caja = Caja::find($this->identificador);
        // dd($this->gasto_id);
        // Guardar datos validados
        $tipoSave = $caja->update([
            'metodo_pago' => $this->metodo_pago,
            'importe' => $this->importe,
            'pedido_id' => $this->pedido_id,
            'poveedor_id' => $this->poveedor_id,
            'fecha' => $this->fecha,
            'tipo_movimiento' => $this->tipo_movimiento,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
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
            'nInterno' => $this->nInterno,
            'nFactura' => $this->nFactura,
            'pagado' => $this->pagado,
            'pendiente' => $this->pendiente,
            'asientoContable' => $this->asientoContable,
            'cuentaContable_id' => $this->cuentaContable_id,
            'gasto_id' => $this->gasto_id == '' ? null : $this->gasto_id

        ]);
        event(new \App\Events\LogEvent(Auth::user(), 53, $caja->id));   

        if ($tipoSave) {
            
            $this->alert('success', '¡Movimiento de caja actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del movimiento de caja!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Movimiento de caja actualizado correctamente.');

        $this->emit('confirmed');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el movimiento de caja? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);

    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('caja.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $Caja = Caja::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 54, $Caja->id));
        $Caja->delete();
        return redirect()->route('caja.index');

    }
}
