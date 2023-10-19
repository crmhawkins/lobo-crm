<?php

namespace App\Http\Livewire\TipoGasto;

use Livewire\Component;
use App\Models\TipoGasto;

class IndexComponent extends Component
{

    public $tipos_gasto;

    public function mount()
    {
        $this->tipos_gasto = TipoGasto::all();
    }
    public function render()
    {
        return view('livewire.tipo-gasto.index-component');
    }
}
