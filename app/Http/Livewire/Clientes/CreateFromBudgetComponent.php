<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Cliente;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Ramsey\Uuid\Type\Integer;
use Illuminate\Support\Facades\Auth;

class CreateFromBudgetComponent extends Component
{

    use LivewireAlert;

    public $clientes;
    public $tipo_cliente = 0; //0 es Particular, 1 es Empresa.
    public $codigo_organo_gestor = "";
    public $codigo_unidad_tramitadora = "";
    public $codigo_oficina_contable = "";
    public $trato = 'M';
    public $nombre;
    public $apellido;
    public $tipoCalle;
    public $calle;
    public $numero = 1;
    public $direccionAdicional1 = "";
    public $direccionAdicional2 = "";
    public $direccionAdicional3 = "";
    public $codigoPostal;
    public $ciudad;
    public $nif;
    public $tlf1;
    public $tlf2 = 0;
    public $tlf3 = 0;
    public $email1;
    public $email2 = "No";
    public $email3 = "No";
    public $confPostal = false;
    public $confEmail = false;
    public $confSms = false;
    public $ident;


    public function mount()
    {
        $this->clientes = Cliente::all();
    }


    public function render()
    {
        return view('livewire.clientes.create-from-budget-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'codigo_organo_gestor' => 'nullable',
                'codigo_unidad_tramitadora' => 'nullable',
                'codigo_oficina_contable' => 'nullable',
                'apellido' => 'nullable',
                'tipoCalle' => 'required',
                'tipo_cliente' => 'required',
                'calle' => 'required',
                'numero' => 'required',
                'codigoPostal' => 'required',
                'ciudad' => 'required',
                'nif' => 'required',
                'tlf1' => 'required',
                'email1' => 'required',
                "confPostal" => 'nullable',
                "confEmail" => 'nullable',
                "confSms" => 'nullable',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellido.required' => 'Los protagonistas son obligatorio.',
                'tipoCalle.required' => 'La cantidad de niños es obligatoria.',
                'calle.required' => 'El nombre de usuario es obligatorio.',
                'numero.required' => 'La contraseña es obligatoria.',
                'codigoPostal.required' => 'El lugar es obligatorio.',
                'ciudad.required' => 'La localidad es obligatoria.',
                'nif.required' => 'El telefono es obligatorio.',
                'tlf1.required' => 'El telefono es obligatorio.',
                'email1.required' => 'El telefono es obligatorio.',

            ]
        );

        // Guardar datos validados
        $clienteSave = Cliente::create(array_merge(
            $validatedData,
            [
                "trato" => $this->trato,
                "direccionAdicional1" => $this->direccionAdicional1,
                "direccionAdicional2" => $this->direccionAdicional2,
                "direccionAdicional3" => $this->direccionAdicional3,
                "tlf2" => $this->tlf2,
                "tlf3" => $this->tlf3,
                "email2" => $this->email2,
                "email3" => $this->email3,
            ]
        ));

        event(new \App\Events\LogEvent(Auth::user(), 8, $clienteSave->id));
        $this->ident = $clienteSave->id;
        // Alertas de guardado exitoso
        if ($clienteSave) {
            $this->alert('success', '¡Usuario registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
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
        session(['datos2' => $this->ident]);
        return redirect()->route('presupuestos.create');
    }
}
