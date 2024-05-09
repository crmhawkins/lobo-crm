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



class EditComponent extends Component
{
	    use LivewireAlert;

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
        $this->banco = $caja->banco;
        $this->delegacion_id = $caja->delegacion_id;
        $this->departamento = $caja->departamento;
        $this->iva = $caja->iva;
        $this->descuento = $caja->descuento;
        $this->retencion = $caja->retencion;
        $this->importe_neto = $caja->importe_neto;
        $this->fecha_vencimiento = $caja->fechaVencimiento;
        $this->fecha_pago = $caja->fechaPago;
        
        $this->cuenta = $caja->cuenta;
        $this->delegaciones = Delegacion::all();
        $this->importeIva = $caja->importeIva;
        $this->total = $caja->total;


    }
    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
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
