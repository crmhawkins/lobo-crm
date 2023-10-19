<?php

namespace App\Http\Livewire\ServiciosPacks;

use App\Models\Servicio;
use App\Models\ServicioPack;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $nombre;
    public $servicios;
    public $servicio;
    public $serviciosPack = [];


    public function mount()
    {
        $pack = ServicioPack::find($this->identificador);
        $this->servicios = Servicio::all();
        $this->serviciosPack = Servicio::where("id_pack", $this->identificador)->get();


        $this->nombre = $pack->nombre;

    }

    public function render()
    {
        return view('livewire.servicios_packs.edit-component');
    }

    public function removeServ($key){
        $servicio = Servicio::where('id', $this->serviciosPack[$key]->id)->first();
        $servicio->update(["id_pack" => null]);
        unset($this->serviciosPack[$key]);
    }

    public function addServ(){
        $servicio = Servicio::where('id', $this->servicio)->first();
        dd($this->serviciosPack['items']);
        // $this->serviciosPack[count($this->serviciosPack)] = $servicio;
        array_push($this->serviciosPack, $servicio);
    }
    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'nombre' => 'required',
        ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);

        // Encuentra el identificador
        $servicioPack = ServicioPack::find($this->identificador);

        // Guardar datos validados
        $servicioSave = $servicioPack->update([
            'nombre' => $this->nombre,
        ]);

        if ($servicioSave) {
            $this->alert('success', 'Usuario actualizado correctamente!', [
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

        session()->flash('message', 'Servicio actualizado correctamente.');

        $this->emit('eventUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el usuario? No hay vuelta atrás', [
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
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('servicios-packs.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        Servicio::where("id_pack", $this->identificador)->update(["id_pack" => null]);
        $pack = ServicioPack::find($this->identificador);
        $pack->delete();
        return redirect()->route('servicios-packs.index');

    }
}
