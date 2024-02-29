<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use App\Models\Alertas;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $clientes;
    public $tipo_cliente = 0; //0 es Particular, 1 es Empresa.
    public $nombre;
    public $dni_cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;
    public $forma_pago_pref = "";
    public $estado;
    public $precio_crema;
    public $precio_vodka07l;
    public $precio_vodka175l;
    public $precio_vodka3l;
    public $nota;
    public $usarDireccionEnvio = false;
    public $direccionenvio;
    public $provinciaenvio;
    public $localidadenvio;
    public $codPostalenvio;
    public $vencimiento_factura_pref = 0;
    public $porcentaje_bloq = 10;
    public $cuenta_contable;

    public function mount()
    {
        $this->clientes = Clients::all();
    }

    public function crearClientes()
    {
        return Redirect::to(route("clientes.create"));
    }



    public function render()
    {
        return view('livewire.clientes.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {

        if (!$this->usarDireccionEnvio) {
            $this->direccionenvio = $this->direccion;
            $this->provinciaenvio = $this->provincia;
            $this->localidadenvio = $this->localidad;
            $this->codPostalenvio = $this->cod_postal;
        }
        // Validación de datos
        $validatedData = $this->validate(
            [
                'tipo_cliente' => 'required',
                'nombre' => 'required',
                'dni_cif' => 'required',
                'direccion' => 'required',
                'provincia' => 'required',
                'localidad' => 'required',
                'cod_postal' => 'required',
                'direccionenvio' => 'required',
                'provinciaenvio' => 'required',
                'localidadenvio' => 'required',
                'codPostalenvio' => 'required',
                'usarDireccionEnvio' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'forma_pago_pref' => 'required',
                'vencimiento_factura_pref' => 'required',
                'nota' => 'nullable',
                'porcentaje_bloq'=> 'nullable',
                'cuenta_contable'=> 'nullable'


            ],
            // Mensajes de error
            [
                'tipo_cliente.required' => 'El tipo de cliente es obligatorio.',
                'nombre.required' => 'El nombre es obligatorio.',
                'dni_cif.required' => 'El documento de identidad es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
                'provincia.required' => 'La provincia es obligatoria.',
                'localidad.required' => 'La localidad es obligatoria.',
                'cod_postal.required' => 'El código es obligatoria.',
                'telefono.required' => 'El telefono es obligatorio.',
                'email.required' => 'El email es obligatorio.',

            ]
        );

        // Guardar datos validados
        $clienteSave = Clients::create($validatedData);

        event(new \App\Events\LogEvent(Auth::user(), 8, $clienteSave->id));

        // Alertas de guardado exitoso
        if ($clienteSave) {

            Alertas::create([
                'user_id' => 13,
                'stage' => 1,
                'titulo' => 'Revisión Pendiente: Nuevo Cliente',
                'descripcion' => 'Nuevo cliente a la espera de aprobación: ' . $clienteSave->nombre,
                'referencia_id' => $clienteSave->id,
                'leida' => null,
            ]);

            $this->alert('success', '¡Cliente registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del cliente!', [
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
        return redirect()->route('clientes.index');
    }
}
