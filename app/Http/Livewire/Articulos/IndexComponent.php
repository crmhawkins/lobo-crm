<?php

namespace App\Http\Livewire\Articulos;

use App\Models\Articulos;
use Livewire\Component;

class IndexComponent extends Component
{
    public $articulos;

    public function mount(){
        $this->articulos = Articulos::all();
    }
    public function render()
    {

        return view('livewire.articulos.index-component');
    }
}
