<?php

namespace App\Http\Livewire\TipoEvento;

use Livewire\Component;
use App\Models\TipoEvento;

class IndexComponent extends Component
{
	public $tipos_evento;

    public function mount()
    {
        $this->tipos_evento = TipoEvento::all();
    }
    public function render()
    {
        return view('livewire.tipo-evento.index-component');
    }
}
