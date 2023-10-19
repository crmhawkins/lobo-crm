<?php

namespace App\Http\Livewire\DepartamentosUser;

use App\Models\DepartamentosUser;
use Livewire\Component;

class IndexComponent extends Component
{
    public $departamentos;

    public function mount()
    {
        $this->departamentos = DepartamentosUser::all();
    }
    public function render()
    {
        return view('livewire.departamentos-user.index-component');
    }
}
