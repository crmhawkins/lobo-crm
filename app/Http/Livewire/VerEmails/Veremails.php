<?php

namespace App\Http\Livewire\VerEmails;

use Livewire\Component;
use App\Models\RegistroEmail;
use App\Models\Clients;
use App\Models\User;
use App\Models\Facturas;
use App\Models\Pedido;
use App\Models\TipoEmails;
class Veremails extends Component
{

    public $registroEmails = [];
    public $clientes = [];
    public $clienteSeleccionadoId = null;
    public $fecha_max = null;
    public $fecha_min = null;
    public $tipoEmails = [];
    public $tipoEmailSeleccionadoId = null;


    public function getTipo($id){

        $tipo = TipoEmails::find($id);
        if($tipo){
            return $tipo->nombre;
        }else{
            return '';
        }

    }

    public function getCliente($id)
    {
        $cliente = Clients::find($id);
        if ($cliente) {
            return $cliente->nombre;
        } else {
            return '';
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user) {
            return $user->name;
        } else {
            return '';
        }
    }
    public function mount()
    {

        $this->registroEmails = RegistroEmail::all();
        $this->clientes = Clients::all();
        $this->tipoEmails = TipoEmails::all();
    }

    public function getPedido($id)
    {
        $pedido = Pedido::find($id);
        if ($pedido) {
            return $pedido->id;
        } else {
            return '-';
        }
    }

    public function getFactura($id)
    {
        $factura = Facturas::find($id);
        if ($factura) {
            return $factura->numero_factura;
        } else {
            return '-';
        }
    }

    public function filtrar()
    {
        //hazlo con query

        $query = RegistroEmail::query();



        if ($this->clienteSeleccionadoId > 0) {
            $query->where('cliente_id', $this->clienteSeleccionadoId);
        }

        if ($this->fecha_min) {
            $query->where('updated_at', '>=', $this->fecha_min);
        }

        if ($this->fecha_max) {
            $query->where('updated_at', '<=', $this->fecha_max);
        }

        if ($this->tipoEmailSeleccionadoId > 0) {
            $query->where('tipo_id', $this->tipoEmailSeleccionadoId);
        }

        $this->registroEmails = $query->get();
    }

    public function limpiarFiltros()
    {
        $this->clienteSeleccionadoId = null;
        $this->fecha_max = null;
        $this->fecha_min = null;
        $this->registroEmails = RegistroEmail::all();
    }

    public function updated($field)
    {
        $this->validateOnly($field, [
            'clienteSeleccionadoId' => 'numeric',
            'fecha_max' => 'date',
            'fecha_min' => 'date',
            'tipoEmailSeleccionadoId' => 'numeric',
        ]);

        if ($field == 'fecha_max' && $this->fecha_min) {
            $this->validateOnly($field, [
                'fecha_max' => 'after_or_equal:fecha_min',
            ]);
        }

        if ($field == 'fecha_min' && $this->fecha_max) {
            $this->validateOnly($field, [
                'fecha_min' => 'before_or_equal:fecha_max',
            ]);
        }


        $this->filtrar();
    }


    public function render()
    {
        return view('livewire.ver-emails.veremails');
    }
}
