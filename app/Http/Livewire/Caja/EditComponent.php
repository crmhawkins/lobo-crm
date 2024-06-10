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
            $this->compensacion = true;
            $this->factura_id =  $this->facturas_compensadas->first()->factura_id;
        }else{
            $this->facturas_compensadas = FacturasCompensadas::where('factura_id', $this->pedido_id)->get();
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

    }
    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
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

            $this->importeIva = $this->importe * $this->iva / 100;
            
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
