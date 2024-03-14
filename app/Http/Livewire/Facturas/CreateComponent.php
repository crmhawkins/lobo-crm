<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Alumno;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Clients;
use App\Models\Facturas;
use App\Policies\ClientsEmailPolicy;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Alertas;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $idpedido;
    public $numero_factura; // 0 por defecto por si no se selecciona ninguno
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descripcion;
    public $estado = "Pendiente";
    public $metodo_pago = "No Pagado";

    public $pedido;
    public $precio;
    public $pedido_id;
    public $cliente;
    public $clientes;
    public $cliente_id;

    public function mount()
    {

            if (isset($this->idpedido)){
            $this->pedido_id = $this->idpedido;
            $this->pedido = Pedido::find($this->idpedido);
            $this->cliente_id = $this->pedido->cliente_id;
            $this->cliente = Clients::find($this->cliente_id);
            $diasVencimiento = $this->cliente->vencimiento_factura_pref;
            $this->fecha_vencimiento = Carbon::now()->addDays($diasVencimiento)->format('Y-m-d');
            $this->metodo_pago = $this->cliente->forma_pago_pref;
            $this->precio = $this->pedido->precio;
            }
        $this->clientes = Clients::where('estado', 2)->get();
        $this->numero_factura = Facturas::count() + 1;
        $this->fecha_emision = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.facturas.create-component');
    }


    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'numero_factura' => 'required',
                'cliente_id' => 'required',
                'pedido_id' => 'nullable',
                'fecha_emision' => 'required',
                'fecha_vencimiento' => '',
                'descripcion' => '',
                'estado' => 'nullable',
                'precio' => 'nullable',
                'metodo_pago' => '',

            ],
            // Mensajes de error
            [
                'numero_factura.required' => 'Indique un nº de factura.',
                'fecha_emision.required' => 'Ingrese una fecha de emisión',
                'id_pedido.min' => 'Seleccione un pedido',
            ]
        );

        // Guardar datos validados
        $facturasSave = Facturas::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 17, $facturasSave->id));
        $pedidosSave =false;
        // Alertas de guardado exitoso
        if (isset($this->idpedido)){
        $pedidosSave = $this->pedido->update(['estado' => 5]);
        }
        if ($facturasSave) {
            if($pedidosSave){
                Alertas::create([
                    'user_id' => 13,
                    'stage' => 3,
                    'titulo' => 'Estado del Pedido: Entregado ',
                    'descripcion' => 'El pedido nº ' . $this->pedido->id . ' ha sido entregado',
                    'referencia_id' => $this->pedido->id,
                    'leida' => null,
                ]);
            }
            $this->alert('success', 'Factura registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la factura!', [
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
            'destroy',
            'listarPedido',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('facturas.index');
    }
}
