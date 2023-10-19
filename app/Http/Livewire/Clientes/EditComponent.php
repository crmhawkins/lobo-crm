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

    public function mount()
    {
        $cliente = Clients::find($this->identificador);


        $this->tipo_cliente = $cliente->tipo_cliente;
        $this->nombre = $cliente->nombre;
        $this->dni_cif = $cliente->dni_cif;
        $this->direccion = $cliente->direccion;
        $this->provincia = $cliente->provincia;
        $this->localidad = $cliente->localidad;
        $this->cod_postal = $cliente->cod_postal;
        $this->telefono = $cliente->telefono;
        $this->email = $cliente->email;
        $this->forma_pago_pref = $cliente->forma_pago_pref;
    }


    public function render()
    {
        return view('livewire.clientes.edit-component');
    }


    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'tipo_cliente' => 'required',
            'nombre' => 'required',
            'dni_cif' => 'required',
            'direccion' => 'required',
            'provincia' => 'required',
            'localidad' => 'required',
            'cod_postal' => 'required',
            'telefono' => 'required',
            'email' => 'required',
            'forma_pago_pref'=> 'nullable',

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
            'telefono' => $this->telefono,
            'email' => $this->email,
            'forma_pago_pref' => $this->forma_pago_pref,
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
