<?php

namespace App\Http\Livewire\Proveedores;

use App\Models\Proveedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Delegacion;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $proveedor;
    public $nombre;
    public $dni_cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;
    public $nota;
    public $cuenta_contable;
    public $delegacion_COD="";
    public $delegaciones;
    public $cuenta;
    public $forma_pago_pref = "";


    public function mount()
    {
        $proveedor = Proveedores::find($this->identificador);
        $this->delegaciones = Delegacion::all();
        $this->nombre = $proveedor->nombre;
        $this->dni_cif = $proveedor->dni_cif;
        $this->cuenta_contable = $proveedor->cuenta_contable;
        $this->cuenta = $proveedor->cuenta;
        $this->forma_pago_pref = $proveedor->forma_pago_pref;
        $this->delegacion_COD = $proveedor->delegacion_COD;
        $this->direccion = $proveedor->direccion;
        $this->provincia = $proveedor->provincia;
        $this->localidad = $proveedor->localidad;
        $this->cod_postal = $proveedor->cod_postal;
        $this->telefono = $proveedor->telefono;
        $this->email = $proveedor->email;
        $this->nota = $proveedor->nota;

    }


    public function render()
    {
        return view('livewire.proveedores.edit-component');
    }


    // Al hacer update en el formulario
    public function update()
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
            ]);

        // Encuentra el identificador
        $proveedor = Proveedores::find($this->identificador);

        // Guardar datos validados
        $proveedorSave = $proveedor->update([
            'nombre'=>$this->nombre,
            'dni_cif' => $this->dni_cif,
            'direccion' => $this->direccion,
            'provincia' => $this->provincia,
            'localidad' => $this->localidad,
            'cod_postal' => $this->cod_postal,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'nota'=> $this->nota,
            'delegacion_COD'=> $this->delegacion_COD,
            'cuenta_contable'=> $this->cuenta_contable,
            'cuenta'=> $this->cuenta,
            'forma_pago_pref' => $this->forma_pago_pref,
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 9, $proveedor->id));

        if ($proveedorSave) {
            $this->alert('success', '¡Proveedor actualizado correctamente!', [
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

        session()->flash('message', 'proveedor actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el proveedor? No hay vuelta atrás', [
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
        return redirect()->route('proveedores.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $proveedor = Proveedores::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 10, $proveedor->id));
        $proveedor->delete();
        return redirect()->route('proveedores.index');

    }
}
