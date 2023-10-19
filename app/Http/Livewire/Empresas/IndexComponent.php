<?php

namespace App\Http\Livewire\Empresas;

use App\Models\Empresa;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $empresas;

    public function mount()
    {
        $this->empresas = Empresa::all();
    }

    public function render()
    {

        return view('livewire.empresas.index-component', [
            'empresas' => $this->empresas,
        ]);
    }

}
