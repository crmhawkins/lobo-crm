<?php

namespace App\Http\Livewire\Comercial;

use App\Models\ClientesComercial;
use App\Models\PedidosComercial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Productos;
use App\Models\User;

use Illuminate\Support\Facades\DB;
//pdf
use Barryvdh\DomPDF\Facade\Pdf;
class pedidos extends Component
{
    public $pedidos;
    public $clientes;
    public $arrFiltrado = [];
    public $clienteSeleccionadoId = -1;
    public $fecha_min = null;
    public $fecha_max = null;
    protected $listeners = ['refreshComponent' => '$refresh'];


    public function getClienteNombre($cliente_id)
    {
        return ClientesComercial::find($cliente_id)->nombre;
    }
    



    public function updatePedidos()
    {
        // Guardar los filtros en la sesión
        session([
            'pedido_filtro_clienteSeleccionadoId' => $this->clienteSeleccionadoId,
            'pedido_filtro_fecha_min' => $this->fecha_min,
            'pedido_filtro_fecha_max' => $this->fecha_max,
        ]);

        $query = PedidosComercial::query();
        
        if (Auth::user()->role == 3) {
                $query->where('comercial_id', Auth::user()->id);
        }

       
        if ($this->clienteSeleccionadoId && $this->clienteSeleccionadoId != -1) {
            $query->where('cliente_id', $this->clienteSeleccionadoId);
        }

        // Si se ha seleccionado un comercial específico (diferente de -1)
        // Filtra los pedidos por los clientes asociados a ese comercial
        // if ($this->comercialSeleccionadoId && $this->comercialSeleccionadoId != -1) {
        //     $query->whereHas('cliente', function ($query) {
        //         $query->where('comercial_id', $this->comercialSeleccionadoId);
        //     });
        // }

        // Rango entre fecha min y fecha max
        if ($this->fecha_min) {
            $query->where('fecha', '>=', $this->fecha_min);
        }
        if ($this->fecha_max) {
            $query->where('fecha', '<=', $this->fecha_max);
        }

        $this->pedidos = $query->get();

        $this->emit('refreshComponent');
    }

    


    public function limpiarFiltros()
    {
        $this->clienteSeleccionadoId = -1;
        $this->fecha_min = null;
        $this->fecha_max = null;

        // Limpiar filtros de la sesión
        session()->forget([
            'pedido_filtro_clienteSeleccionadoId',
            'pedido_filtro_fecha_min',
            'pedido_filtro_fecha_max'
        ]);

        $this->updatePedidos();
    }

    public function updated($propertyName)
    {
        if (
            $propertyName == 'clienteSeleccionadoId' ||
            $propertyName == 'fecha_min' ||
            $propertyName == 'fecha_max'
        ) {
            $this->updatePedidos();
        }
    }

    public function mount()
    {
        // Recuperar filtros desde la sesión si existen
        $this->clienteSeleccionadoId = session('pedido_filtro_clienteSeleccionadoId', -1);
        $this->fecha_min = session('pedido_filtro_fecha_min', null);
        $this->fecha_max = session('pedido_filtro_fecha_max', null);

        if (Auth::user()->role != 3) {
            $this->pedidos = PedidosComercial::all();
        } else {
            $this->pedidos = PedidosComercial::where('comercial_id', Auth::user()->id)->get();

        }
        $this->clientes = ClientesComercial::all();

        if (Auth::user()->role == 3) {
           
            $this->clientes = ClientesComercial::where('comercial_id', Auth::user()->id)->get();
            $this->updatePedidos();
        }else{
            $this->updatePedidos();

        }
    }


    public function render()
    {
        return view('livewire.comercial.pedidos');
    }

 
}
