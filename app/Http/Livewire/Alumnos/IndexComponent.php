<?php

namespace App\Http\Livewire\Alumnos;

use App\Models\Alumno;
use App\Models\Empresa;
use Livewire\Component;


class IndexComponent extends Component
{
    // public $search;
    public $alumnos;

    public $empresas;

    public function mount()
    {
        $this->alumnos = Alumno::all();
        $this->empresas = Empresa::all();
    }

    public function render()
    {

        return view('livewire.alumnos.index-component');
    }

}
