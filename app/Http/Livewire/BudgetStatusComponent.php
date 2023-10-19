<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Budget_statu;


class BudgetStatusComponent extends Component
{
    use WithPagination;

    public $response;
    public $sort = 'id';
    public $direction ='asc';
    public $titulo;

    public function render()
    {
        $status = Budget_statu::where('created_at', '!=', null)
                            ->orderBy($this->sort, $this->direction)
                            ->paginate(5);

       

        return view('livewire.budget-status-component', compact('status'));
    }
    public function title($titulo) {
        $this->titulo = $titulo;
    }

    public function order($sort) {

        if ($this->sort == $sort) {

            if ($this->direction == 'desc') {
                $this->direction = 'asc';
            } else {
                $this->direction = 'desc';
            }

        } else {
            $this->sort = $sort;
            $this->direction = 'asc';
        }
        
    }
}
