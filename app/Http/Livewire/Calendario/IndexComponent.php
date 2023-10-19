<?php

namespace App\Http\Livewire\Calendario;

use App\Models\Evento;
use App\Models\Presupuesto;
use App\Models\TipoEvento;
use Livewire\Component;

class IndexComponent extends Component
{
    public $eventos;
    public $presupuestos;
    public $categorias;


    public function mount()
    {
        $this->eventos = Evento::all();
        $this->presupuestos = Presupuesto::all();
        $this->categorias = TipoEvento::all();
    }
    public function render()
    {
        return view('livewire.calendario.index-component');
    }
}
