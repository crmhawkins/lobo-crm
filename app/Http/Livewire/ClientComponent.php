<?php

namespace App\Http\Livewire;
use App\Models\Clients;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ClientComponent extends Component
{

    public $clientes;

    public function mount()
    {
        $this->clientes = Clients::all();
    }

    public function render()
    {
        return view('livewire.clients.client-component', [
            'clientes' => $this->clientes,
        ]);
    }

    
}
