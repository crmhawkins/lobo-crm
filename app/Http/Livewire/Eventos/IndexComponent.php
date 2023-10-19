<?php

namespace App\Http\Livewire\Eventos;

use App\Models\Evento;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $eventos;

    public function mount()
    {
        $this->eventos = Evento::all();
    }

    public function render()
    {

        return view('livewire.eventos.index-component');
    }

}
