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

class CreateGastoComponent extends Component
{
    use LivewireAlert;

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
    public $iva;
    public $descuento;
    public $retencion;
    public $importe_neto;
    public $fecha_vencimiento;
    public $fecha_pago;
    public $cuenta;

    public function mount()
    {
        $this->poveedores = Proveedores::all();
        $this->clientes = Clients::all();
        $this->delegaciones = Delegacion::all();

    }
    public function render()
    {
        return view('livewire.caja.create-gasto-component');
    }
    public function submit()
    {
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


            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]
        );

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
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 52, $usuariosSave->id));

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
