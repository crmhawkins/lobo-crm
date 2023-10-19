<?php

namespace App\Http\Livewire\Caja;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\Presupuesto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\TipoEvento;
use Illuminate\Support\Facades\Auth;
class CreateIngresoComponent extends Component
{
    use LivewireAlert;

    public $tipo_movimiento = 'Ingreso';
    public $metodo_pago;
    public $importe;
    public $descripcion;
    public $presupuesto_id;
    public $fecha;
    public $clientes;
    public $categorias;
    public $presupuestos;
    public $eventos;


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->categorias = TipoEvento::all();
        $this->eventos = Evento::all();
        $this->clientes = Cliente::all();
    }
    public function render()
    {
        return view('livewire.caja.create-ingreso-component');
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
                'presupuesto_id' => 'required',
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

    public function getCliente($id)
    {
        return $this->clientes->firstWhere('id', $this->presupuestos->firstWhere('id', $id)->id_cliente)->nombre . " " . $this->clientes->firstWhere('id', $this->presupuestos->firstWhere('id', $id)->id_cliente)->apellido;
    }
}
