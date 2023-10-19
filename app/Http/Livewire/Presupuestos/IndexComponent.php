<?php

namespace App\Http\Livewire\Presupuestos;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Evento;
use App\Models\Presupuesto;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $presupuestos;
    public $clientes;
    public $eventos;
    public $empresas;


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->clientes = Cliente::all();
        $this->eventos = Evento::all();
    }

    public function getClienteNombre($id){
        $cliente = $this->clientes->find($id);

        $nombre = $cliente->nombre;
        $apellido = $cliente->apellido;

        return "$nombre $apellido";
    }

    public function render()
    {

        return view('livewire.presupuestos.index-component');
    }

}
