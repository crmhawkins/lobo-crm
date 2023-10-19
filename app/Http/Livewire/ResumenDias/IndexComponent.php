<?php

namespace App\Http\Livewire\ResumenMensual;

use App\Models\Evento;
use App\Models\GastosMensuales;
use App\Models\Monitor;
use App\Models\Programa;
use App\Models\Servicio;
use App\Models\ServicioEvento;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $meses;
    public $eventos;
    public $servicios;
    public $monitores;
    public $serviciosEvento;

    public function mount()
    {
        $this->meses = GastosMensuales::all();

    }

    public function nombreEvento(int $id){
        $evento = $this->serviciosEvento->where("id", $id)->first();
        
        return $this->eventos->find($evento->id_evento)->eventoNombre;
    }

    public function nombreServicio(int $id){
        $servicio = $this->serviciosEvento->find($id)->id_servicio;
        return $this->servicios->find($servicio)->nombre;
    }

    public function nombreMonitor(int $id){
        $nombreCompleto =  sprintf("%s %s", $this->monitores->find($id)->nombre,  $this->monitores->find($id)->apellidos);
        return $nombreCompleto;
    }


    public function render()
    {

        return view('livewire.resumen-mensual.index-component');
    }

}