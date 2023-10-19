<?php

namespace App\Http\Livewire\Programas;

use App\Models\Evento;
use App\Models\Monitor;
use App\Models\Programa;
use App\Models\Servicio;
use App\Models\ServicioEvento;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $programas;
    public $eventos;
    public $servicios;
    public $monitores;
    public $serviciosEvento;

    public function mount()
    {
        $this->programas = Programa::all();
        $this->servicios = Servicio::all();
        $this->eventos = Evento::all();
        $this->monitores = Monitor::all();
        $this->serviciosEvento = ServicioEvento::all();

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

        return view('livewire.programas.index-component');
    }

}