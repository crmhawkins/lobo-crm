<?php

namespace App\Http\Livewire;

use Livewire\Component;

class BudgetComponent extends Component
{
    public $response;

    public function render()
    {
        return view('livewire.budget-component');
    }

    public function title($titulo) {
        $this->response = $titulo;
    }
}
