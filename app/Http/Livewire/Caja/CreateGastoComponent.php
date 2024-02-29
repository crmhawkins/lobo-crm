<?php

namespace App\Http\Livewire\Caja;

use App\Models\Clients;
use App\Models\Proveedores;
use App\Models\Facturas;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Caja;;
use Illuminate\Support\Facades\Auth;

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


    public function mount()
    {
        $this->poveedores = Proveedores::all();
        $this->clientes = Clients::all();

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


            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]
        );

        // Guardar datos validados
        $usuariosSave = Caja::create($validatedData);
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
