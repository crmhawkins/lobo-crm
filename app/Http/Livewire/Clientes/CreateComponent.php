<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use App\Models\Alertas;
use App\Models\Delegacion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Productos;
use App\Models\ProductoPrecioCliente;
use App\Models\AnotacionesClientePedido;
use App\Models\Emails;
use App\Models\SubCuentaHijo;
use App\Models\SubCuentaContable;

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
    public $vencimiento_factura_pref = "7";
    public $porcentaje_bloq = 10;
    public $cuenta_contable;
    public $cuenta;
    public $delegacion_COD="";
    public $delegaciones;
    public $comercial_id="";
    public $comerciales;
    public $productos;
    public $arrProductos;
    public $observaciones;
    public $anotacionesProximoPedido;
    public $emailAnadir;
    public $emails = [];
    public $credito;


    public function anadirEmail(){
        //dd("prueba");
        if($this->emailAnadir != ""){

            $this->emails[] = $this->emailAnadir;
            $this->emailAnadir = "";
        }
    }

    public function mount()
    {
        $this->clientes = Clients::all();
        $this->comerciales = User::whereIn('role', [2, 3])->get();
        $this->delegaciones = Delegacion::all();
        $this->productos = Productos::orderByRaw("CASE WHEN orden IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'orden' al final
        ->orderBy('orden', 'asc')  // Ordenar primero por orden
        ->orderByRaw("CASE WHEN grupo IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'grupo' al final
        ->orderBy('grupo', 'asc')  // Luego ordenar por grupo
        ->orderBy('nombre', 'asc')  // Finalmente, ordenar alfabéticamente por nombre
        ->get();

        //array asociativo donde cada producto es una clave y el precio es el valor
        $this->arrProductos = [];
        foreach ($this->productos as $producto) {
            $this->arrProductos[$producto->id] = 0;
        }

    }

    public function updated($property){
        if($property === 'delegacion_COD' ){

            //delegacion_COD debe ser un numero de 2 cifras, si es 1, se añade un 0 delante
            if(strlen($this->delegacion_COD) == 1){
                $cod = '0'.$this->delegacion_COD;
            }else{
                $cod = $this->delegacion_COD;
            }

            //ver el ultimo cliente creado y ver su numero de cuenta contable, que empieza por 700 y añadirle el codigo de delegacion
            $ultimoCliente = Clients::whereNotNull('cuenta_contable')->latest()->first();
            //dd($ultimoCliente);

            $numeroCuenta = $ultimoCliente->cuenta_contable;
            //coger el numero y quitarle el 700 y los 2 siguentes numeros
            $numeroCuenta = substr($numeroCuenta, 5);
            //pasarlo a entero
            $numeroCuenta = (int)$numeroCuenta;
            //sumarle 1 al numero sobrante
            $numeroCuenta = $numeroCuenta + 1;

            $this->cuenta_contable = '700'.$cod.$numeroCuenta;

        }
    }

    public function crearClientes()
    {
        return Redirect::to(route("clientes.create"));
    }

    public function render()
    {
        return view('livewire.clientes.create-component');
    }


    public function eliminarEmail($index){
        unset($this->emails[$index]);
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

        if($this->emails == null){
            $this->alert('error', '¡Debe añadir al menos un email!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }
        $this->email = $this->emails[0];
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
                'cuenta_contable'=> 'nullable',
                'delegacion_COD'=> 'nullable',
                'comercial_id'=> 'nullable',
                'cuenta'=> 'nullable',
                'observaciones'=> 'nullable',
                'credito'=> 'nullable',


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

        if($this->cuenta_contable != null){
            $cuentaContable = Clients::where('cuenta_contable', $this->cuenta_contable)->first();

            if($cuentaContable){
                $this->alert('error', '¡La cuenta contable ya existe!', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);
                return;
            }

        }

        // Guardar datos validados
        $clienteSave = Clients::create($validatedData);


        if($clienteSave){

            if($this->cuenta_contable != null){
                $subcuentaContable = SubCuentaContable::where('numero', 7000)->first();

                if($subcuentaContable != null){
                    $subcuenta = SubCuentaHijo::create([
                        'sub_cuenta_id' => $subcuentaContable->id,
                        'numero' => $clienteSave->cuenta_contable,
                        'nombre' => $clienteSave->nombre,
                        'descripcion' => 'Cliente',
                    ]);
                }
            }


            if($this->emails != null){
                foreach ($this->emails as $email) {
                    $email1 = new Emails();
                    $email1->email = $email;
                    $email1->cliente_id = $clienteSave->id;
                    $email1->save();

                }
            }

            foreach ($this->arrProductos as $key => $value) {
                   $precioProductosSave =  ProductoPrecioCliente::create([
                        'cliente_id' => $clienteSave->id,
                        'producto_id' => $key,
                        'precio' => $value
                    ]);

            }
        }

        //event(new \App\Events\LogEvent(Auth::user(), 59, $clienteSave->id));

        // Alertas de guardado exitoso
        if ($clienteSave) {

            if(isset($this->anotacionesProximoPedido) && $this->anotacionesProximoPedido != ""){
                $anotacion = new AnotacionesClientePedido();
                $anotacion->cliente_id = $clienteSave->id;
                $anotacion->anotacion = $this->anotacionesProximoPedido;
                $anotacion->save();
            }

            Alertas::create([
                'user_id' => 13,
                'stage' => 1,
                'titulo' => 'Revisión Pendiente: Nuevo Cliente',
                'descripcion' => 'Nuevo cliente a la espera de aprobación: ' . $clienteSave->nombre,
                'referencia_id' => $clienteSave->id,
                'leida' => null,
            ]);

            $dGeneral = User::where('id', 13)->first();
            $administrativo1 = User::where('id', 17)->first();
            $administrativo2 = User::where('id', 18)->first();

            $data = [['type' => 'text', 'text' => $clienteSave->nombre]];
            $buttondata = [$clienteSave->id];

            if(isset($dGeneral) && $dGeneral->telefono != null){
                $phone = '+34'.$dGeneral->telefono;
                enviarMensajeWhatsApp('cliente_pendiente', $data, $buttondata, $phone);
            }

            if(isset($administrativo1) && $administrativo1->telefono != null){
                $phone = '+34'.$administrativo1->telefono;
                enviarMensajeWhatsApp('cliente_pendiente', $data, $buttondata, $phone);
            }

            if(isset($administrativo2) && $administrativo2->telefono != null){
                $phone = '+34'.$administrativo2->telefono;
                enviarMensajeWhatsApp('cliente_pendiente', $data, $buttondata, $phone);
            }




            $this->alert('success', '¡Cliente registrado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del cliente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function crearCuentaContable(){

        $ultimoCliente = Clients::whereNotNull('cuenta_contable')->latest()->first();
        //dd($ultimoCliente);

        $numeroCuenta = $ultimoCliente->cuenta_contable;
        //coger el numero y quitarle el 700 y los 2 siguentes numeros
        $numeroCuenta = substr($numeroCuenta, 5);
        //pasarlo a entero
        $numeroCuenta = (int)$numeroCuenta;
        //sumarle 1 al numero sobrante
        $numeroCuenta = $numeroCuenta + 1;

        $this->cuenta_contable = '700'.$numeroCuenta;
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
