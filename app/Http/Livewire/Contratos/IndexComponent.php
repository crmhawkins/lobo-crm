<?php

namespace App\Http\Livewire\Contratos;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Evento;
use App\Models\MetodoPago;
use App\Models\Presupuesto;
use App\Models\ServicioEvento;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class IndexComponent extends Component
{
    // public $search;
    public $contratos;
    public $eventos;
    public $clientes;
    public $metodosPago;
    public $presupuestos;

    public function mount()
    {
        if(session()->has('descarga')){
            return response()->download(public_path() . session('descarga'));
        }

        $this->contratos = Contrato::all();
        $this->eventos = Evento::all();
        $this->clientes = Cliente::all();
        $this->metodosPago = MetodoPago::all();
        $this->presupuestos = Presupuesto::all();
    }

    public function nombreEvento($id){
        $presupuesto = $this->presupuestos->find($id);
       

        return $this->eventos->find($presupuesto->id_evento)->eventoNombre;
    }

    public function descargarPDF($ruta){
        return response()->download(public_path() . $ruta);
    }
    public function nombreApellidoCliente ($id){
        $presupuesto = $this->presupuestos->find($id);
        $cliente = $this->clientes->find($presupuesto->id_cliente);
        $nombreCompleto = "$cliente->nombre $cliente->apellido ";
        return $nombreCompleto;
    }

    public function nifCliente ($id){
        $presupuesto = $this->presupuestos->find($id);
        $cliente = $this->clientes->find($presupuesto->id_cliente);
        $nif = $cliente->nif;
        return $nif;
    }

    public function getTotalDesc ($id){
        $descuento = $this->presupuestos->find($id)->descuento;
     
        return $descuento;
    }

    public function getTotal ($id){
 
        $descuento = $this->presupuestos->find($id)->precioFinal;
     
        return $descuento;
    }

    public function metodoPago($id){
        return $this->metodosPago->find($id)->nombre;
    }

    

    public function render()
    {
        return view('livewire.contratos.index-component');
    }

}
