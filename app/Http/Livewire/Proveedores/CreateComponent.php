<?php

namespace App\Http\Livewire\Proveedores;

use App\Models\Proveedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Delegacion;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\DepartamentosProveedores;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;
use App\Helpers\GlobalFunctions;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $proveedores;

    public $nombre;
    public $dni_cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;
    public $cuenta_contable;
    public $delegacion_COD="";
    public $delegaciones;
    public $cuenta;
    public $forma_pago_pref = "";

    public $nota;

    public $departamentos;
    public $departamentoSeleccionado;
    public $departamento_id;
    public $cuentaContable_id;
    public $cuentasContables;

    public function mount()
    {
        $this->proveedores = Proveedores::all();
        $this->delegaciones = Delegacion::all();
        $this->departamentos = DepartamentosProveedores::all();
        $this->cuentasContables = GlobalFunctions::loadCuentasContables();

    }

    public function crearProveedores()
    {
        return Redirect::to(route("proveedores.create"));
    }


    public function updated($property){
        if($property == 'cuentaContable_id' || $property == 'delegacion_COD'){
            //dd(GlobalFunctions::findCuentaByNumero($this->cuentaContable_id));
            if($this->cuentaContable_id != null && $this->delegacion_COD != null){
                //dd($this->cuentaContable_id);
                if(strlen($this->delegacion_COD) == 1){
                    $cod = '0'.$this->delegacion_COD;
                }else{
                    $cod = $this->delegacion_COD;
                }
                $this->crearCuentaContable($cod);
            }
        }
    }

    public function crearCuentaContable($cod)
    {
        // Buscar el último proveedor cuya cuenta contable comience con cuentaContable_id seguido del código de delegación
        $ultimoCliente = Proveedores::where('cuenta_contable', 'LIKE', $this->cuentaContable_id . '%')
                                    ->whereNotNull('cuenta_contable')
                                    ->latest()->first();
        
        if ($ultimoCliente) {
            // Obtener la cuenta contable sin los primeros 5 caracteres (cuentaContable_id + cod)
            $numeroCliente = substr($ultimoCliente->cuenta_contable, 5);

            // Convertir a número entero
            $numeroCliente = (int)$numeroCliente;

            // Incrementar el número de cliente
            $numeroCliente = $numeroCliente + 1;
        } else {
            // Si no hay ningún cliente con cuenta contable similar, comenzamos con el número de cliente 1
            $numeroCliente = 1;
        }

        // Formatear el número de cliente para que tenga 3 dígitos
        $numeroCliente = str_pad($numeroCliente, 3, '0', STR_PAD_LEFT);

        // Crear la nueva cuenta contable concatenando cuentaContable_id, delegación y el número de cliente
        $this->cuenta_contable = $this->cuentaContable_id . $cod . $numeroCliente;
    }


    public function render()
    {
        return view('livewire.proveedores.create-component');
    }

   

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'dni_cif' => 'required',
                'direccion' => 'required',
                'provincia' => 'required',
                'localidad' => 'required',
                'cod_postal' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'nota' => 'nullable',
                'cuenta_contable'=> 'nullable',
                'delegacion_COD'=> 'nullable',
                'cuenta'=> 'nullable',
                'forma_pago_pref' => 'nullable',
                'departamento_id' => 'nullable',
                'cuenta_contable_numero' => 'nullable'

            ],
            // Mensajes de error
            [
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
        $proveedoresSave = Proveedores::create($validatedData);

        event(new \App\Events\LogEvent(Auth::user(), 8, $proveedoresSave->id));

        // Alertas de guardado exitoso
        if ($proveedoresSave) {
            $this->alert('success', '¡Proveedor registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del proveedor!', [
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
        return redirect()->route('proveedores.index');
    }
}
