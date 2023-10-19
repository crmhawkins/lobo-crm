<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\User;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $usuarios;

    public function mount()
    {
        $this->usuarios = User::all();
    }

    public function render()
    {

        return view('livewire.usuarios.index-component');
    }

}
