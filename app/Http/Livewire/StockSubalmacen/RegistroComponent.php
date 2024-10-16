<?php

namespace App\Http\Livewire\StockSubalmacen;

use App\Models\StockSubalmacen;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class RegistroComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedMonth; // Propiedad para el mes seleccionado

    protected $paginationTheme = 'bootstrap';

    // Actualizar la búsqueda cuando se cambia el término
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Renderizar la vista con los datos filtrados
    public function render()
    {
        $query = StockSubalmacen::query();

        // Filtro por búsqueda de productos y subalmacenes
        if ($this->search) {
            $query->whereHas('producto', function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('subalmacen', function ($query) {
                $query->where('almacen', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por mes si está seleccionado
        if ($this->selectedMonth) {
            $month = Carbon::createFromFormat('Y-m', $this->selectedMonth)->month;
            $year = Carbon::createFromFormat('Y-m', $this->selectedMonth)->year;

            $query->whereMonth('fecha', $month)
                  ->whereYear('fecha', $year);
        }

        // Obtener los registros paginados
        $registros = $query->orderBy('fecha', 'desc')->paginate($this->perPage);

        return view('livewire.stock-subalmacen.registro-component', [
            'registros' => $registros,
        ]);
    }
}
