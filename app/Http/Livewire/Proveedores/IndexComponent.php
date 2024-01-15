<?php

namespace App\Http\Livewire\Proveedores;

use App\Models\Proveedores;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $proveedores;

    public function mount()
    {
        $this->proveedores = Proveedores::all();
    }

    public function render()
    {

        return view('livewire.proveedores.index-component');
    }

}
