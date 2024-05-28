<?php

namespace App\Http\Livewire\Configuracion;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\DepartamentosProveedores;

class EditComponent extends Component
{
    use LivewireAlert;

    public $cuenta;
    public $configuracion;
    public $departamentos = [];
    public $nombreDepartamento;


    public function mount($configuracion)
    {
        $this->configuracion = $configuracion;
        $this->cuenta = $configuracion->cuenta;
        $this->departamentos = DepartamentosProveedores::all();

    }

    public function addDepartamento(){
        $this->validate([
            'nombreDepartamento' => 'required'
        ]);
        $departamento = DepartamentosProveedores::create([
            'nombre' => $this->nombreDepartamento,
            'descripcion' => ''
        ]);

        $this->departamentos = DepartamentosProveedores::all();
    }

    public function removeDepartamento($id){
        $departamento = DepartamentosProveedores::find($id);
        $departamento->delete();
        $this->departamentos = DepartamentosProveedores::all();
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
