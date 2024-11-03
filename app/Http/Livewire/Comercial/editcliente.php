<?php

namespace App\Http\Livewire\Comercial;

use App\Models\ClientesComercial;
use App\Models\acuerdosComerciales;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Redirect;
use App\Models\Clients;
use App\Models\Delegacion;

class editcliente extends Component
{
    use LivewireAlert;

    public $identificador;
    public $acuerdos;
    public $cliente;
    public $nombre;
    public $cif;
    public $direccion;
    public $provincia;
    public $localidad;
    public $cod_postal;
    public $telefono;
    public $email;
    public $comercial_id;
    public $distribuidores = [];
    public $distribuidor_id;
    public $delegaciones;
    public $delegacion_id;

    public function mount()
    {
        $cliente = ClientesComercial::find($this->identificador);
        $this->cliente = $cliente;
        $this->comercial_id = $cliente->comercial_id;
        $this->nombre = $cliente->nombre;
        $this->cif = $cliente->cif;
        $this->direccion = $cliente->direccion;
        $this->provincia = $cliente->provincia;
        $this->localidad = $cliente->localidad;
        $this->cod_postal = $cliente->cod_postal;
        $this->telefono = $cliente->telefono;
        $this->email = $cliente->email;
        $this->distribuidor_id = $cliente->distribuidor_id;
        $this->distribuidores = Clients::all();
        $this->acuerdos = acuerdosComerciales::where('cliente_id', $this->identificador)->get();
        $this->delegaciones = Delegacion::all();
        $this->delegacion_id = $cliente->delegacion_id;
        //dd($this->emailsExistentes);
    }

    public function render()
    {
        return view('livewire.comercial.editcliente');
    }
    // Al hacer update en el formulario
    public function update()
    {


        // Encuentra el identificador
        $cliente = ClientesComercial::find($this->identificador);
        // Guardar datos validados
        $clienteSave = $cliente->update([
            'nombre' => $this->nombre,
            'cif' => $this->cif,
            'direccion' => $this->direccion,
            'provincia' => $this->provincia,
            'localidad' => $this->localidad,
            'cod_postal' => $this->cod_postal,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'comercial_id' => $this->comercial_id,
            'distribuidor_id' => $this->distribuidor_id,
            'delegacion_id' => $this->delegacion_id,

        ]);
        //event(new \App\Events\LogEvent(Auth::user(), 9, $cliente->id));

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
    public function destroy()
    {

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
        return redirect()->route('comercial.clientes');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {

        $cliente = ClientesComercial::find($this->identificador);
        //event(new \App\Events\LogEvent(Auth::user(), 10, $cliente->id));

        $cliente->delete();
        //event(new \App\Events\LogEvent(Auth::user(), 10, $cliente->id));

        return redirect()->route('comercial.clientes');
    }
}
