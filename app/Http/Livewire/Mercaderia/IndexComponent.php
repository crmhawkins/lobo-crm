<?php

namespace App\Http\Livewire\Mercaderia;

use App\Models\Presupuesto;
use App\Models\Mercaderia;
use App\Models\MercaderiaCategoria;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $mercaderias;
    public $categorias;

    public $categoria_id;

    public function mount()
    {
        $this->mercaderias = Mercaderia::all();
        $this->categorias = MercaderiaCategoria::all();
    }

    public function render()
    {
        return view('livewire.mercaderia.index-component');
    }

    public function getCategoria($id){
        return $this->categorias->where('id', $id)->first()->nombre;
    }

    public function cambioCategoria(){
        if($this->categoria_id == 0){
            $this->mercaderias = Mercaderia::all();
        }else{
            $this->mercaderias = Mercaderia::where('categoria_id', $this->categoria_id)->get();
        }
        $this->emit('refreshComponent');
    }

    public function getListeners()
    {
        return [
            'cambioCategoria',
            'refreshComponent' => '$refresh',
        ];
    }

}
