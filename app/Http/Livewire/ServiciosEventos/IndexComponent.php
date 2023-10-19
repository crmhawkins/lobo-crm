<?php

namespace App\Http\Livewire\ServiciosEventos;

use App\Models\Evento;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $servicios;

    public function mount()
    {
        $this->servicios = Evento::find($this->identificador)->servicio()->orderBy('name')->get();
    }

    public function render()
    {

        return view('livewire.servicios_eventos.index-component');
    }

}