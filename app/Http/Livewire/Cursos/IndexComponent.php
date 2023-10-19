<?php

namespace App\Http\Livewire\Cursos;

use App\Models\Cursos;
use App\Models\CursosCelebracion;
use App\Models\CursosDenominacion;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $cursos;

    public $cursos_celebracion;
    public $cursos_denominacion;

    public function mount()
    {
        $this->cursos = Cursos::all();
        $this->cursos_denominacion = CursosDenominacion::all();
        $this->cursos_celebracion = CursosCelebracion::all();
    }

    public function render()
    {

        return view('livewire.cursos.index-component');
    }

}
