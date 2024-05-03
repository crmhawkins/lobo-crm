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


    public function mount()
    {

        $this->facturas = Facturas::where('estado', 'Pendiente')->get();
        $this->clientes = Clients::all();
        $this->bancos = Bancos::all();
    }
    public function render()
    {
        if($this->banco){
            $this->bancoSeleccionado = Bancos::find($this->banco);
        }

        return view('livewire.caja.create-ingreso-component');

    }
    public function submit()
    {
        if(count($this->bancos) > 0){
            // Validación de datos
            $validatedData = $this->validate(
                [
                    'tipo_movimiento' => 'required',
                    'metodo_pago' => 'required',
                    'importe' => 'required',
                    'descripcion' => 'required',
                    'pedido_id' => 'required',
                    'fecha' => 'required',


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
        }else{
            $validatedData = $this->validate(
                [
                    'tipo_movimiento' => 'required',
                    'metodo_pago' => 'required',
                    'importe' => 'required',
                    'descripcion' => 'required',
                    'pedido_id' => 'required',
                    'fecha' => 'required',
                    'banco' => 'required',
                ],
                // Mensajes de error
                [
                    'tipo_movimiento.required' => 'El tipo de movimiento es obligatorio.',
                    'metodo_pago.required' => 'El método de pago es obligatorio.',
                    'importe.required' => 'El importe es obligatorio.',
                    'descripcion.required' => 'La descripción es obligatoria.',
                    'pedido_id.required' => 'El pedido es obligatorio.',
                    'fecha.required' => 'La fecha es obligatoria.',
                    'banco.required' => 'El banco es obligatorio.',

                ]
            );
        }
        // Guardar datos validados
        $usuariosSave = Caja::create([
            'tipo_movimiento' => $this->tipo_movimiento,
            'metodo_pago' => $this->metodo_pago,
            'importe' => $this->importe,
            'descripcion' => $this->descripcion,
            'pedido_id' => $this->pedido_id,
            'fecha' => $this->fecha,
            'banco' => $this->banco,
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

    public function getCliente($id)
    {
         return $this->clientes->firstWhere('id', $id)->nombre;
    }
}
