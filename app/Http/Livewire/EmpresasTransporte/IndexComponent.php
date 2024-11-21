<?php

namespace App\Http\Livewire\EmpresasTransporte;

use App\Models\EmpresasTransporte;
use Livewire\Component;

class IndexComponent extends Component
{
    public $nombreEmpresa;
    public $empresas;
    public $empresaId;

    public function mount()
    {
        $this->empresas = EmpresasTransporte::all();
    }

    public function render()
    {
        return view('livewire.empresas-transporte.index-component', [
            'empresas' => $this->empresas,
        ]);
    }

    public function addEmpresa()
    {
        $this->validate([
            'nombreEmpresa' => 'required|string|max:255',
        ]);

        EmpresasTransporte::create([
            'nombre' => $this->nombreEmpresa,
        ]);

        $this->reset('nombreEmpresa');
        $this->empresas = EmpresasTransporte::all();
    }

    public function editEmpresa($id)
    {
        $empresa = EmpresasTransporte::find($id);
        $this->empresaId = $empresa->id;
        $this->nombreEmpresa = $empresa->nombre;
    }

    public function updateEmpresa()
    {
        $this->validate([
            'nombreEmpresa' => 'required|string|max:255',
        ]);

        $empresa = EmpresasTransporte::find($this->empresaId);
        $empresa->update([
            'nombre' => $this->nombreEmpresa,
        ]);

        $this->reset(['nombreEmpresa', 'empresaId']);
        $this->empresas = EmpresasTransporte::all();
    }

    public function deleteEmpresa($id)
    {
        EmpresasTransporte::find($id)->delete();
        $this->empresas = EmpresasTransporte::all();
    }
}
