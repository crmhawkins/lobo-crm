<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Clients;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class IndexComponent extends Component
{
    // public $search;
    public $clientes;

    public function mount()
    {
        if(Auth::user()->role != 3){
            $this->clientes = Clients::all();
        }else{
            $this->clientes = Clients::where('comercial_id', Auth::user()->id)->get();
        }
    }

    public function render()
    {

        return view('livewire.clientes.index-component');
    }

}
