<?php

namespace App\Http\Livewire\Servicios;

use App\Models\Servicio;
use App\Models\ServicioCategoria;
use App\Models\ServicioPack;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $servicios;
    public $serviciosCategoria;
    public $serviciosPack;

    public function mount()
    {
        $this->servicios = Servicio::all();
        $this->serviciosCategoria = ServicioCategoria::all();
        $this->serviciosPack = ServicioPack::all();
    }

    public function precioTotal(int $id){
        $servicio = $this->servicios->find($id);
        return $servicio->minMonitor * $servicio->precioMonitor + $servicio->precioBase;

    }

    public function nombreCategoria(int $id){
        return $this->serviciosCategoria->where("id", $id)->first()->nombre;
    }

    public function nombrePack(int $id){
        return $this->serviciosPack->find($id)->nombre;
    }

    public function render()
    {
        return view('livewire.servicios.index-component');
    }

}