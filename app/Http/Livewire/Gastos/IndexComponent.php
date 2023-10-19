<?php

namespace App\Http\Livewire\Gastos;

use App\Models\Presupuesto;
use App\Models\Gastos;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $gastos;


    public function mount()
    {
        $this->gastos = Gastos::all();
    }

    public function render()
    {

        return view('livewire.gastos.index-component');
    }

}
