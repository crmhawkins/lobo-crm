<?php

namespace App\Http\Livewire\Configuracion;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditComponent extends Component
{
    use LivewireAlert;

    public $cuenta;
    public $configuracion;

    public function mount($configuracion)
    {
        $this->configuracion = $configuracion;
        $this->cuenta = $configuracion->cuenta;
    }

    public function update()
    {
        $this->validate([
            'cuenta' => 'required'
        ]);

        $this->configuracion->update([
            'cuenta' => $this->cuenta
        ]);

        $this->alert('success', 'Configuración actualizada con éxito');
    }


     //function getListeners update

    protected $listeners = [
        'update' => 'update'
    ];






    public function render()
    {
        return view('livewire.configuracion.edit-component');
    }
}
