<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use App\Models\Delegacion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductoPrecioCliente;
use App\Models\Productos;
use App\Models\AnotacionesClientePedido;
use App\Models\Emails;
use App\Helpers\GlobalFunctions;
use App\Models\acuerdosComerciales;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $acuerdos;
    public $clientes;
    public $cliente;
    public $tipo_cliente; //0 es Particular, 1 es Empresa.
    public $nombre;
    public $dni_cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;
    public $forma_pago_pref;
    public $estado;
    public $precio_crema;
    public $precio_vodka07l;
    public $precio_vodka175l;
    public $precio_vodka3l;
    public $nota;
    public $usarDireccionEnvio;
    public $direccionenvio;
    public $provinciaenvio;
    public $localidadenvio;
    public $codPostalenvio;
    public $vencimiento_factura_pref;
    public $porcentaje_bloq;
    public $cuenta_contable;
    public $delegacion_COD;
    public $delegaciones;
    public $comercial_id;
    public $comerciales;
    public $cuenta;
    public $observaciones;


    public $productos;
    public $productosAsignados;
    public $arrProductos;

    public $anotacionesProximoPedido = [];
    public $anotacion;

    public $emailAnadir;
    public $emails = [];
    public $emailsExistentes = [];
    public $credito;
    public $cuentaContable_id;
    public $cuentasContables;

    public function anadirEmail(){
        //dd("prueba");
        if($this->emailAnadir != ""){
           
            $this->emails[] = $this->emailAnadir;
            $this->emailAnadir = "";
        }
    }


    public function mount()
    {
        $cliente = Clients::find($this->identificador);
        $this->cliente = $cliente;
        $this->comerciales = User::whereIn('role', [2, 3])->get();
        $this->delegaciones = Delegacion::all();
        $this->comercial_id = $cliente->comercial_id;
        $this->delegacion_COD = $cliente->delegacion_COD;
        $this->tipo_cliente = $cliente->tipo_cliente;
        $this->cuenta = $cliente->cuenta;
        $this->cuenta_contable = $cliente->cuenta_contable;
        $this->porcentaje_bloq = $cliente->porcentaje_bloq;
        $this->nombre = $cliente->nombre;
        $this->dni_cif = $cliente->dni_cif;
        $this->direccion = $cliente->direccion;
        $this->provincia = $cliente->provincia;
        $this->localidad = $cliente->localidad;
        $this->cod_postal = $cliente->cod_postal;
        $this->telefono = $cliente->telefono;
        $this->email = $cliente->email;
        $this->forma_pago_pref = $cliente->forma_pago_pref;
        $this->estado = $cliente->estado;
        $this->precio_crema = $cliente->precio_crema;
        $this->precio_vodka07l = $cliente->precio_vodka07l;
        $this->precio_vodka175l = $cliente->precio_vodka175l;
        $this->precio_vodka3l = $cliente->precio_vodka3l;
        $this->nota = $cliente->nota;
        $this->usarDireccionEnvio= $cliente->usarDireccionEnvio;
        $this->direccionenvio = $cliente->direccionenvio;
        $this->provinciaenvio = $cliente->provinciaenvio;
        $this->localidadenvio = $cliente->localidadenvio;
        $this->codPostalenvio = $cliente->codPostalenvio;
        $this->vencimiento_factura_pref = $cliente->vencimiento_factura_pref;
        $this->observaciones = $cliente->observaciones;
        $this->productos = Productos::orderByRaw("CASE WHEN orden IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'orden' al final
        ->orderBy('orden', 'asc')  // Ordenar primero por orden
        ->orderByRaw("CASE WHEN grupo IS NULL THEN 1 ELSE 0 END")  // Los NULL en 'grupo' al final
        ->orderBy('grupo', 'asc')  // Luego ordenar por grupo
        ->orderBy('nombre', 'asc')  // Finalmente, ordenar alfabéticamente por nombre
        ->get();    
        $this->productosAsignados =  ProductoPrecioCliente::where('cliente_id', $this->identificador)->get();
        $this->arrProductos = [];
        $this->credito = $cliente->credito;
        $this->cuentasContables = GlobalFunctions::loadCuentasContables();
        $this->cuentaContable_id = $cliente->cuenta_contable;
        $this->emailsExistentes = Emails::where('cliente_id', $this->identificador)->get();
        $this->acuerdos = acuerdosComerciales::where('cliente_id', $this->identificador)->get();
        
        //dd($this->emailsExistentes);


        foreach ($this->productos as $producto) {
            $this->arrProductos[$producto->id] = $this->productosAsignados->where('producto_id', $producto->id)->first() ? $this->productosAsignados->where('producto_id', $producto->id)->first()->precio : 0;
        }
        $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->identificador)->where('estado', 'pendiente')->get();
    }


    public function eliminarEmail($index){
        unset($this->emails[$index]);
    }

    public function eliminarEmailExistente($id){
        $email = Emails::find($id);
        $email->delete();
        $this->emailsExistentes = Emails::where('cliente_id', $this->identificador)->get();
    }

    public function render()
    {
        return view('livewire.clientes.edit-component');
    }


    public function completarAnotacion($id){
        $anotacion = AnotacionesClientePedido::find($id);
        $anotacion->update([
            'estado' => 'completado'
        ]);
        $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->identificador)->where('estado', 'pendiente')->get();
        //event(new \App\Events\LogEvent(Auth::user(), 60, $this->identificador));
    }

    public function updated($property){
        if($property === 'delegacion_COD' ){

            //delegacion_COD debe ser un numero de 2 cifras, si es 1, se añade un 0 delante
            if(strlen($this->delegacion_COD) == 1){
                $cod = '0'.$this->delegacion_COD;
            }else{
                $cod = $this->delegacion_COD;
            }

            //comprobar si hay cuenta contable, si no hay, se crea una nueva
            if($this->cuenta_contable == null){
                $this->crearCuentaContable($cod);
            }else{
                //si hay cuenta contable, se cambia el codigo de delegacion
                //$this->cuenta_contable = substr_replace($this->cuenta_contable, $cod, 3, 2);
                //dd($this->cuenta_contable);
            }

           
        }
    }

    public function crearCuentaContable($cod){
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


    public function addAnotacion(){
        if($this->anotacion !== null){
            $anotacion = AnotacionesClientePedido::create([
                'cliente_id' => $this->identificador,
                'anotacion' => $this->anotacion,
                'estado' => 'pendiente'
            ]);
            $this->anotacionesProximoPedido = AnotacionesClientePedido::where('cliente_id', $this->identificador)->where('estado', 'pendiente')->get();
            $this->anotacion = null;
           // event(new \App\Events\LogEvent(Auth::user(), 61, $this->identificador));

        }
    }

    // Al hacer update en el formulario
    public function update()
    {
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
                'usarDireccionEnvio' => 'nullable',
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
                'precio_crema'=> 'El precio es obligatorio.',
                'precio_vodka07l'=> 'El precio es obligatorio.',
                'precio_vodka175l'=> 'El precio es obligatorio.',
                'precio_vodka3l'=> 'El precio es obligatorio.',
            ]);

        // Encuentra el identificador
        $cliente = Clients::find($this->identificador);

        //comprobar que la cuenta contable no se duplique, por lo que buscamos si ya existe sin contar este cliente
        $cuentaContable = Clients::where('cuenta_contable', $this->cuenta_contable)->where('id', '!=', $this->identificador)->first();

        if($cuentaContable){
            $this->alert('error', '¡La cuenta contable ya existe!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }

        // Guardar datos validados
        $clienteSave = $cliente->update([
            'tipo_cliente' => $this->tipo_cliente,
            'nombre'=>$this->nombre,
            'dni_cif' => $this->dni_cif,
            'direccion' => $this->direccion,
            'provincia' => $this->provincia,
            'localidad' => $this->localidad,
            'cod_postal' => $this->cod_postal,
            'direccionenvio' => $this->direccionenvio,
            'provinciaenvio' => $this->provinciaenvio,
            'localidadenvio' => $this->localidadenvio,
            'codPostalenvio' => $this->codPostalenvio,
            'usarDireccionEnvio' => $this->usarDireccionEnvio,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'forma_pago_pref' => $this->forma_pago_pref,
            'vencimiento_factura_pref' => $this->vencimiento_factura_pref,
            'estado' => $this->estado,
            'precio_crema'=> $this->precio_crema,
            'precio_vodka07l'=>$this->precio_vodka07l,
            'precio_vodka175l'=>$this->precio_vodka175l,
            'precio_vodka3l'=> $this->precio_vodka3l,
            'nota'=> $this->nota,
            'porcentaje_bloq'=> $this->porcentaje_bloq,
            'delegacion_COD'=> $this->delegacion_COD,
            'comercial_id'=> $this->comercial_id,
            'cuenta_contable'=> $this->cuenta_contable,
            'cuenta'=> $this->cuenta,
            'observaciones'=> $this->observaciones,
            'credito'=> $this->credito,
        ]);
        //event(new \App\Events\LogEvent(Auth::user(), 9, $cliente->id));

        if($clienteSave){

            if($this->emails != null){
                foreach ($this->emails as $email) {
                    $email1 = new Emails();
                    $email1->email = $email;
                    $email1->cliente_id = $cliente->id;
                    $email1->save();
                   
                }
            }
            foreach ($this->arrProductos as $key => $value) {
                   
                $productoPrecioCliente = ProductoPrecioCliente::where('cliente_id', $cliente->id)->where('producto_id', $key)->first();
                if($productoPrecioCliente){
                    $productoPrecioCliente->update([
                        'precio' => $value
                    ]);
                }else{
                    $precioProductosSave =  ProductoPrecioCliente::create([
                        'cliente_id' => $cliente->id,
                        'producto_id' => $key,
                        'precio' => $value
                    ]);
                }
            }
            }
        if ($clienteSave) {

            $this->alert('success', '¡Cliente actualizado correctamente!', [
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

        session()->flash('message', 'cliente actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el cliente? No hay vuelta atrás', [
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
            'update',
            'destroy',
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('clientes.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        
        $cliente = Clients::find($this->identificador);
        //event(new \App\Events\LogEvent(Auth::user(), 10, $cliente->id));

        if ($cliente){
             $productos = ProductoPrecioCliente::where('cliente_id', $cliente->id)->get();
                foreach ($productos as $producto) {
                    $producto->delete();
                }
            $anotaciones = AnotacionesClientePedido::where('cliente_id', $cliente->id)->get();
            foreach ($anotaciones as $anotacion) {
                $anotacion->delete();
            }
        }

        $cliente->delete();
        //event(new \App\Events\LogEvent(Auth::user(), 10, $cliente->id));

        return redirect()->route('clientes.index');

    }
}
