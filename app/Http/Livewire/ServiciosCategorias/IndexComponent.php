<?php

namespace App\Http\Livewire\ServiciosCategorias;

use App\Models\ServicioCategoria;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $serviciosCategorias;

    public function mount()
    {
        $this->serviciosCategorias = ServicioCategoria::all();
    }

    public function render()
    {

        return view('livewire.servicios_categorias.index-component');
    }

}