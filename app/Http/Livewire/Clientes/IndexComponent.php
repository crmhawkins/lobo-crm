<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $clientes;

    public function mount()
    {
        $this->clientes = Clients::all();
    }

    public function render()
    {

        return view('livewire.clientes.index-component');
    }

}
