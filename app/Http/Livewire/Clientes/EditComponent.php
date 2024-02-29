<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $clientes;
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

    public function mount()
    {
        $cliente = Clients::find($this->identificador);


        $this->tipo_cliente = $cliente->tipo_cliente;
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
    }


    public function render()
    {
        return view('livewire.clientes.edit-component');
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
                'precio_crema'=> 'El precio es obligatorio.',
                'precio_vodka07l'=> 'El precio es obligatorio.',
                'precio_vodka175l'=> 'El precio es obligatorio.',
                'precio_vodka3l'=> 'El precio es obligatorio.',
            ]);

        // Encuentra el identificador
        $cliente = Clients::find($this->identificador);

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
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 9, $cliente->id));

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
        event(new \App\Events\LogEvent(Auth::user(), 10, $cliente->id));
        $cliente->delete();
        return redirect()->route('clientes.index');

    }
}
