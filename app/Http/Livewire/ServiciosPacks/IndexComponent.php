<?php

namespace App\Http\Livewire\ServiciosPacks;

use App\Models\ServicioPack;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $serviciosPacks;

    public function mount()
    {
        $this->serviciosPacks = ServicioPack::all();
    }

    public function render()
    {

        return view('livewire.servicios_packs.index-component');
    }

}