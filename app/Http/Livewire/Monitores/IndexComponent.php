<?php

namespace App\Http\Livewire\Monitores;

use App\Models\Monitor;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $monitores;

    public function mount()
    {
        $this->monitores = Monitor::all();
    }

    public function render()
    {

        return view('livewire.monitores.index-component');
    }

}
