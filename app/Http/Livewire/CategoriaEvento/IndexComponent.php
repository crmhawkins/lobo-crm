<?php

namespace App\Http\Livewire\CategoriaEvento;

use Livewire\Component;
use App\Models\CategoriaEvento;

class IndexComponent extends Component
{

    public $categorias_evento;

    public function mount()
    {
        $this->categorias_evento = CategoriaEvento::all();
    }
    public function render()
    {
        return view('livewire.categoria-evento.index-component');
    }
}
