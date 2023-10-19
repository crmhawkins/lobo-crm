<?php

namespace App\Http\Livewire\Iva;

use Livewire\Component;
use App\Models\Iva;


class IndexComponent extends Component

{

    public $iva;

    public function mount()
    {
        $this->iva = Iva::all();
    }
    public function render()
    {
        return view('livewire.iva.index-component', [
            'iva' => $this->iva,
        ]);
        // return view('livewire.productos-component');
    }
}
