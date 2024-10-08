<?php

namespace App\Http\Livewire\ControlPresupuestario;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Facturas;

class VentasComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $fechaMin;
    public $fechaMax;
    public $perPage = 25;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFechaMin()
    {
        $this->resetPage();
    }

    public function updatingFechaMax()
    {
        $this->resetPage();
    }

    public function loadVentas()
    {
        // Inicia la consulta base de facturas
        $ventasQuery = Facturas::whereYear('created_at', Carbon::now()->year);

        // Agrega búsqueda si está definida
        if ($this->search) {
            $ventasQuery->where(function($query) {
                $query->where('numero_factura', 'like', '%' . $this->search . '%')
                      ->orWhereHas('cliente', function($query) {
                          $query->where('nombre', 'like', '%' . $this->search . '%');
                      });
            });
        }

        // Agrega el filtro de fecha mínima y máxima si están definidos
        if ($this->fechaMin && $this->fechaMax) {
            $ventasQuery->whereBetween('created_at', [$this->fechaMin, $this->fechaMax]);
        } elseif ($this->fechaMin) {
            $ventasQuery->whereDate('created_at', '>=', $this->fechaMin);
        } elseif ($this->fechaMax) {
            $ventasQuery->whereDate('created_at', '<=', $this->fechaMax);
        }

        // Paginamos los resultados según el valor definido (25 por defecto)
        return $ventasQuery->paginate($this->perPage);
    }

    public function render()
    {
        $facturas = $this->loadVentas();

        return view('livewire.control-presupuestario.ventas-component', [
            'facturas' => $facturas,
        ]);
    }
}
